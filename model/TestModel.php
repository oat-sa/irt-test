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

use common_exception_NotImplemented;
use common_report_Report;
use taoTests_models_classes_TestsService;
use taoTests_models_classes_TestModel;
use common_ext_ExtensionsManager;
use core_kernel_classes_Resource;
use tao_helpers_form_GenerisTreeForm;
use core_kernel_classes_Class;
use core_kernel_classes_Property;
use tao_models_classes_service_FileStorage;
use oat\irtTest\model\routing\Plan;

/**
 * An abstract implementation of a TAO Test Model, representing an IRT Test Model with a routing plan
 * logic to be implemented.
 *
 * @author Joel Bout <joel@taotesting.com>
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 */
abstract class TestModel implements taoTests_models_classes_TestModel {
    
    /**
     * A reference on the common_ext_Extension object representing the
     * irtTest extension.
     * 
     * @var common_ext_Extension
     */
    private $ext;
    
    /**
     * Create a new TestModel object dedicated to IRT Tests.
     */
    public function __construct() {
        $this->ext = common_ext_ExtensionsManager::singleton()->getExtensionById('irtTest');
    }
    
    /**
     * This method is called when the label of a TAO Test Resource with the IRT Test Model is
     * changed in the ontology.
     *
     * @param Resource $test The $test, as a Generis resource wich is the target of the label change.
     */
    public function onChangeTestLabel(core_kernel_classes_Resource $test) {
        
    }
    
    /**
     * Bind the items represented by the $items parameter, to a given IRT $test.
     *
     * @param core_kernel_classes_Resource $test A Generis resource representing the IRT Test you want to bind items to.
     * @param array $items An array of core_kernel_classes_Resource object representing the items in the ontology.
     */
    public function prepareContent(core_kernel_classes_Resource $test, $items = array()) {
        TestContent::setItems($test, $items);
    }
    
    /**
     * Delete a given $test, and all its related resources (items, assets, ...).
     *
     * @param core_kernel_classes_Resource $test The Generis resource representing the test to be deleted.
     */
    public function deleteContent(core_kernel_classes_Resource $test) {
        $content = TestContent::getContent($test);
        $content->delete();
        $test->removePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP), $content);
    }
    
    /**
     * Returns all the items potenially used within the given IRT $test definition.
     *
     * @param core_kernel_classes_Resource $test A Generis resource representing the test you want to get the items.
     * @return array An array of core_kernel_classes_Resource objects representing the items related to $test.
     */
    public function getItems(core_kernel_classes_Resource $test) {
        return TestContent::getItems($test);
    }
    
    /**
     * Renders the IRT Test Auhoring tool.
     *
     * @param core_kernel_classes_Resource $test A Generis resource representing the test you want to render the authoring.
     * @return string The authoring tool rendering.
     */
    public function getAuthoring(core_kernel_classes_Resource $test) {
        
        $content = TestContent::getContent($test);
        $tree = tao_helpers_form_GenerisTreeForm::buildTree($content, new core_kernel_classes_Property(PROPERTY_IRT_TEST_CONTENT_ITEMS));
        return $tree->render();
    }
    
    /**
     * Clones the content of $source test to $destination test.
     * This method assumes that $destination has no content.
     *
     * @param core_kernel_classes_Resource $source
     * @param core_kernel_classes_Resource $destination
     */
    public function cloneContent(core_kernel_classes_Resource $source, core_kernel_classes_Resource $destination) {
        throw new common_exception_NotImplemented();
    }
    
    /**
     * Returns the compiler class of the test.
     *
     * @return string
     */
    public function getCompilerClass() {
        return 'oat\irtTest\model\TestAssembler';
    }

    /**
     * Create a Plan object for a given set of $items.
     * 
     * @param array $items An array of core_kernel_classes_Resource objects representing the items involved in the Plan.
     * @return Plan
     */
    public abstract function createRoutingPlan(array $items);
}