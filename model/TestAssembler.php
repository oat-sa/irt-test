<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

namespace oat\irtTest\model;

use taoTests_models_classes_TestCompiler;
use common_report_Report;
use common_Utils;
use common_Logger;
use core_kernel_classes_Resource;
use tao_models_classes_service_ServiceCall;
use tao_models_classes_service_ConstantParameter;
use taoTests_models_classes_TestsService;

/**
 * The TestAssembler is in charge to compile an IRT Test into an atomic assembly to be
 * retrieved at test delivery time.
 * 
 * This assembly, that will be written into the TestAssembler's private storage
 * directory, with the name TestAssembler::ASSEMBLY_FILENAME, is a PHP file included
 * at runtime, describing the IRT Test to be run from a pure runtime perspective.
 *
 * @author Joel Bout <joel@taotesting.com>
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * 
 */
class TestAssembler extends taoTests_models_classes_TestCompiler 
{
    /**
     * The complete file name (name + extension) of the Assembly file
     * to be writtent/read to/from the compilation storage resource.
     * 
     * @var string
     */
    const ASSEMBLY_FILENAME = 'data.php';

    /**
     * Compile the IRT Test definition itself and all the related items. The IRT Test definition
     * will be stored in the TestAssembler's private storage directory, with the name
     * TestAssembler::ASSEMBLY_FILENAME, as a PHP file that will be included at runtime.
     * 
     * The compile() method will return a common_report_Report object  which describes how the compilation
     * process took place (success, failure, warnings, ...) and contains a reference to a ServiceCall object.
     * 
     * The ServiceCall object can be retrieved through the common_report_Report::getData() method. It represents
     * how the compiled test can be consumed as a service e.g. later on at delivery time.
     * 
     * @return common_report_Report A Report containing information about the compilation process, and a tao_models_classes_service_ServicalCall object as its data attribute.
     * @see tao_models_classes_service_ServiceCall TAO's Service Call class.
     */
    public function compile() 
    {
        $testService = taoTests_models_classes_TestsService::singleton();
        $testModel = $testService->getTestModelImplementation($testService->getTestModel($this->getResource()));
        
        $report = new common_report_Report(common_report_Report::TYPE_SUCCESS);
        
        // Instantiate the private directory data.
        $private = $this->spawnPrivateDirectory();

        // Prepare the Item ServiceCalls.
        $items = $testModel->getItems($this->getResource());
        $itemServiceCalls = array();
        
        foreach ($items as $item) {
            
            $compilerClass = $this->getSubCompilerClass($item);
            $compiler = new $compilerClass($item, $this->getStorage());
            $subReport = $compiler->compile();
            $report->add($subReport);
            
            if ($subReport->getType() != common_report_Report::TYPE_SUCCESS) {
                $report->setType($subReport->getType());
                break;
            } else {
                $itemServiceCalls[] = array('item' => $item, 'call' => $subReport->getData());
            }
        }
        
        // Prepare the compiled information about how the test has to be delivered.
        $plan = $testModel->createRoutingPlan($itemServiceCalls, $this->getStorage());
        $fileName = $private->getPath() . self::ASSEMBLY_FILENAME;
        
        file_put_contents($fileName, '<?php return ' . common_Utils::toPHPVariableString(array(
            'routingPlan' => $plan
        )) . ';');
        
         // get Decision engine
         // tell decision engine to prepare meta-data
         
         if ($report->getType() == common_report_Report::TYPE_SUCCESS) {
             $service = new tao_models_classes_service_ServiceCall(new core_kernel_classes_Resource(INSTANCE_IRTTEST_TESTRUNNERSERVICE));
             

             // Reference to the test definition resource in ontology (not usefull in our context but for others?)
             // --> Needed for results transmission.
             $param = new tao_models_classes_service_ConstantParameter(
                 new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_IRTTEST_DEFINITION),
                 $this->getResource()->getUri()
             );
             $service->addInParameter($param);
             
             // Compilation folder where the assembly is stored.
             $param = new tao_models_classes_service_ConstantParameter(
                 new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_IRTTEST_COMPILATION),
                 $private->getId()
             );
             $service->addInParameter($param);
             
             $report->setData($service);
             
         }
         return $report;
    }
}