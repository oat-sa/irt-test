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

namespace oat\irtTest\helpers;

use oat\irtTest\model\routing\Plan;
use tao_helpers_ServiceJavascripts;
use tao_models_classes_service_ServiceCall;

/**
 * The TestContext class provides the utility methods aiming at generating
 * a Test Context usable by the client-side.
 * 
 * This Test Context contains the information needed by the client side to
 * execute the current Item of the Test Flow.
 * 
 * The Test Context is composed of the following entries:
 * 
 * * itemServiceApi: A string representation of the JavaScript TAO Service Call of the current Item.
 * * nextUrl: The URL to be dereference to move to the next Item in the Item Flow.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class TestContext
{
    /**
     * Build the Test Context as an associative array, suitable for a later json_encode().
     * 
     * @param \oat\irtTest\model\routing\Plan $plan The Plan to be respected by the Test.
     * @param string $itemId The unique identifier of the Item to be taken by the candidate. 
     * @param string $testServiceCallId The unique identifier of the Item Service Call.
     * @param string $testDefinitionUri The Uniform Resource Identifier (URI) of the Test Definition.
     * @param string $testCompilationUri The Uniform Resource Identifier (URI) of the Compilation Test Directory.
     * @return array An associative array to be JSON encoded later, for transmission to the client.
     */
    static public function buildContext(Plan $plan, $itemId, $testServiceCallId, $testDefinitionUri, $testCompilationUri)
    {
        $context = array();
        
        if ($itemId !== '') {
            $context['itemServiceApi'] = self::createItemServiceApi($plan->restoreItemRunner($itemId), $itemId, $testServiceCallId);
        } else {
            $context['itemServiceApi'] = null;
        }
        
        $context['nextUrl'] = self::createNextUrl($testServiceCallId, $testDefinitionUri, $testCompilationUri);
        
        return $context;
    }
    
    /**
     * Create the JavaScript code to be executed by the client to call the current Item to be taken by the candidate
     * as a TAO service.
     * 
     * @param tao_models_classes_service_ServiceCall $serviceCall The Item Service Call object.
     * @param string $itemId The unique identifier of the Item to be taken by the candidate through the Service Call.
     * @param string $testServiceCallId The unique identifier of the Service Call to be invoked.
     * @return string The corresponding JavaScript code.
     */
    static private function createItemServiceApi(tao_models_classes_service_ServiceCall $serviceCall, $itemId, $testServiceCallId)
    {
        return tao_helpers_ServiceJavascripts::getServiceApi($serviceCall, "${testServiceCallId}.${itemId}");
    }
    
    /**
     * Create the Uniform Resource Locator (URL) to be dereferenced in order to move to the next Item to 
     * be taken by the candidate.
     * 
     * @param string $testServiceCallId The unique identifier of the Test Service Call.
     * @param string $testDefinitionUri The Uniform Resource Identifier (URI) of the Test currently taken by the candidate.
     * @param string $testCompilationUri The Uniform Resource Identifier (URI) of the Test Compilation Directory.
     * @return string A Uniform Resource Locator (URL).
     */
    static private function createNextUrl($testServiceCallId, $testDefinitionUri, $testCompilationUri)
    {
        $testServiceCallId = urlencode($testServiceCallId);
        $testDefinitionUri = urlencode($testDefinitionUri);
        $testCompilationUri = urlencode($testCompilationUri);
        $standalone = 'true';
        
        return BASE_URL . "TestRunner/next?serviceCallId=${testServiceCallId}&Test=${testDefinitionUri}&Compilation=${testCompilationUri}";
    }
}