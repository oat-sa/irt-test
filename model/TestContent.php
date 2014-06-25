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

/**
 * The Item Response Theorie test content
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQtiTest
 
 */
class TestContent
{
    public static function setItems($test, $items) {
        $content = self::getContent($test);
        $content->editPropertyValues(new core_kernel_classes_Property(PROPERTY_IRT_TEST_CONTENT_ITEMS), $items); 
    }
    
    public static function getItems($test) {
        $content = self::getContent($test);
        
        $items = array();
        $prop = new core_kernel_classes_Property(PROPERTY_IRT_TEST_CONTENT_ITEMS);
        foreach ($content->getPropertyValues($prop) as $itemUri) {
            $items[] = new core_kernel_classes_Resource($itemUri);
        }
        return $items;
    }
    
    public static function getContent($test) {
        $props = $test->getPropertyValues(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
        if (count($props) > 1) {
            throw new \common_Exception();
        }
        
        if (count($props) == 1) {
            $content = new core_kernel_classes_Resource(current($props));
        } else {
            $class = new core_kernel_classes_Class(CLASS_IRT_TEST_CONTENT);
            $content = $class->createInstance();
            $test->setPropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP), $content);
        }
        return $content;
    }
}