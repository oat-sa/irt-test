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
 * 
 */

namespace oat\irtTest\model;

use taoTests_models_classes_TestCompiler;
use common_report_Report;
use core_kernel_classes_Resource;
use tao_models_classes_service_ServiceCall;
use tao_models_classes_service_ConstantParameter;

/**
 * The Item Response Theorie test model
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQtiTest
 
 */
class TestAssembler
	extends taoTests_models_classes_TestCompiler
{
    const ASSEMBLY_FILENAME = 'data.php'; 
    
    /**
     * (non-PHPdoc)
     * @see tao_models_classes_Compiler::compile()
     */
    public function compile() {

        $testService = \taoTests_models_classes_TestsService::singleton();
        $testModel = $testService->getTestModelImplementation($testService->getTestModel($this->getResource()));
        
        $report = new common_report_Report(common_report_Report::TYPE_SUCCESS);

        //prepare the items
        $items = $testModel->getItems($this->getResource());
        $itemRunners = array();
        foreach ($items as $item) {
            $compilerClass = $this->getSubCompilerClass($item);
            $compiler = new $compilerClass($item, $this->getStorage());
            $subReport = $compiler->compile();
            $report->add($subReport);
            if ($subReport->getType() != common_report_Report::TYPE_SUCCESS) {
                $report->setType($subReport->getType());
                break;
            } else {
                $itemRunners[$item->getUri()] = $subReport->getData()->serializeToString();
            }
        }
        
        // prepare the metadata
        $plan = $testModel->getRoutingPlan($items, $this->getStorage());
        
        $private = $this->spawnPrivateDirectory();
        $fileName = $private->getPath().self::ASSEMBLY_FILENAME;
        
        file_put_contents($fileName, '<?php return '.\common_Utils::toPHPVariableString(array(
            'itemRunners' => $itemRunners,
            'routingPlan' => $plan
        )).';');
        
        
         // get Decision engine
         // tell decision engine to prepare meta-data
         
         if ($report->getType() == common_report_Report::TYPE_SUCCESS) {
             $service = new tao_models_classes_service_ServiceCall(new core_kernel_classes_Resource(INSTANCE_IRTTEST_TESTRUNNERSERVICE));
             // item runners
             $param = new tao_models_classes_service_ConstantParameter(
                 new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_IRTTEST_DEFINITION),
                 $this->getResource()->getUri()
             );
             // decision engine to use
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