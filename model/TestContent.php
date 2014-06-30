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

use core_kernel_classes_Class;
use core_kernel_classes_Property;
use core_kernel_classes_Resource;
use common_Exception;

/**
 * A model helper class prodiving utility methods to deal
 * with contents of IRT Tests.
 *
 * @author Joel Bout <joel@taotesting.com>
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * 
 */
class TestContent {
    
    /**
     * Set the $items belonging to $test and bind them in the ontology.
     * 
     * @param core_kernel_classes_Resource $test A Generis resource describing the test you want to bind the $items to.
     * @param array $items An array of core_kernel_classes_Resource objects representing the items to be bound to $test.
     */
    public static function setItems(core_kernel_classes_Resource $test, array $items) {
        $content = self::getContent($test);
        $content->editPropertyValues(new core_kernel_classes_Property(PROPERTY_IRT_TEST_CONTENT_ITEMS), $items); 
    }
    
    /**
     * Get the items composing a given $test.
     * 
     * @param core_kernel_classes_Resource $test A Generis resource describing the test you want to get the items.
     * @return array An array of core_kernel_classes_Resource objects representing the items bound to $test.
     */
    public static function getItems(core_kernel_classes_Resource $test) {
        $content = self::getContent($test);
        
        $items = array();
        $prop = new core_kernel_classes_Property(PROPERTY_IRT_TEST_CONTENT_ITEMS);
        foreach ($content->getPropertyValues($prop) as $itemUri) {
            $items[] = new core_kernel_classes_Resource($itemUri);
        }
        return $items;
    }
    
    /**
     * Get the Generis resource representing the content of $test.
     * 
     * @param core_kernel_classes_Resource $test The test you want to get the content.
     * @throws common_Exception If multiple content instances are bound to $test.
     * @return core_kernel_classes_Resource The Generis resource representing the content $test.
     */
    public static function getContent(core_kernel_classes_Resource $test) {
        $props = $test->getPropertyValues(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
        if (count($props) > 1) {
            throw new common_Exception();
        }
        
        if (count($props) == 1) {
            $content = new core_kernel_classes_Resource(current($props));
        } 
        else {
            $class = new core_kernel_classes_Class(CLASS_IRT_TEST_CONTENT);
            $content = $class->createInstance();
            $test->setPropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP), $content);
        }
        
        return $content;
    }
}