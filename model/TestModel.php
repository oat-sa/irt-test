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

use common_report_Report;
use taoTests_models_classes_TestsService;
use taoTests_models_classes_TestModel;
use common_ext_ExtensionsManager;
use core_kernel_classes_Resource;
use Renderer;
use tao_helpers_form_GenerisTreeForm;
use core_kernel_classes_Class;
use core_kernel_classes_Property;
use tao_models_classes_service_FileStorage;
use oat\irtTest\model\routing\Plan;

/**
 * The Item Response Theorie test model
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQtiTest
 
 */
abstract class TestModel
	implements taoTests_models_classes_TestModel
{
    private $ext;
    
    /**
     * 
     */
    public function __construct() {
        $this->ext = common_ext_ExtensionsManager::singleton()->getExtensionById('irtTest');
    }
    
    /**
     * Called when the label of a test changes
     *
     * @param Resource $test
    */
    public function onChangeTestLabel( core_kernel_classes_Resource $test) {
        
    }
    
    /**
     * Prepare the content of the test,
     * using the provided items if possible
     *
     * @param core_kernel_classes_Resource $test
     * @param array $items
    */
    public function prepareContent( core_kernel_classes_Resource $test, $items = array()) {
        TestContent::setItems($test, $items);
    }
    
    /**
     * Delete the content of the test
     *
     * @param Resource $test
    */
    public function deleteContent( core_kernel_classes_Resource $test) {
        $content = TestContent::getContent($test);
        $content->delete();
        $test->removePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP), $content);
    }
    
    /**
     * Returns all the items potenially used within the test
     *
     * @param Resource $test
     * @return array an array of item resources
    */
    public function getItems( core_kernel_classes_Resource $test) {
        return TestContent::getItems($test);
    }
    
    /**
     * renders the test authoring
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource test
     * @return string
    */
    public function getAuthoring( core_kernel_classes_Resource $test) {
        
        $content = TestContent::getContent($test);
        $tree = tao_helpers_form_GenerisTreeForm::buildTree($content, new \core_kernel_classes_Property(PROPERTY_IRT_TEST_CONTENT_ITEMS));
        return $tree->render();
    }
    
    /**
     * Clones the content of one test to another test,
     * assumes that other test has already been cleaned (using deleteContent())
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param core_kernel_classes_Resource $source
     * @param core_kernel_classes_Resource $destination
    */
    public function cloneContent( core_kernel_classes_Resource $source, core_kernel_classes_Resource $destination) {
        
    }
    
    /**
     * Returns the compiler class of the test
     *
     * @return string
    */
    public function getCompilerClass() {
        return 'oat\irtTest\model\TestAssembler';
    }

    /**
     * Get the routing plan for the adaptiv item
     * 
     * @param array $itemPool
     * @param tao_models_classes_service_FileStorage $storage
     * @return Plan
     */
    public abstract function getRoutingPlan($itemPool, tao_models_classes_service_FileStorage $storage);
}