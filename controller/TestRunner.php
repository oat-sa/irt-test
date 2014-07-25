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

namespace oat\irtTest\controller;

use common_session_SessionManager;
use tao_actions_ServiceModule;
use taoResultServer_models_classes_ResultServerStateFull;
use oat\irtTest\model\TestAssembler;
use oat\irtTest\model\routing\Plan;
use oat\irtTest\model\routing\Route;
use oat\irtTest\helpers\TestContext;

/**
 * The TestRunner is the controller dedicated to deliver an IRT Test
 * to a given candidate. This controller extends tao_actions_ServiceModule and is 
 * then able to access TAO's persistent state storage API internally to deal with
 * the persistency of the test's state.
 * 
 * * The index action is used as a bootstrap, when the TestRunner is called as a service.
 * * On the other hand, the next action is called within an Ajax context to update the candidate's client when the next item has to be delivered.
 * 
 * @author Joel Bout <joel@taotesting.com>
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 */
class TestRunner extends tao_actions_ServiceModule
{
    
    /**
     * An associative array representing the Test Assembly.
     * 
     * @var array
     */
    private $assembly = null;
    
    /**
     * The current state of the Test (current item, ...).
     * 
     * @var string
     */
    private $state = null;
    
    /**
     * The Route object providing the items to be taken by the candidate.
     * 
     * @var \oat\irtTest\model\routing\Route
     */
    private $route = null;
    
    /**
     * Get the PHP associative array representing the IRT Test assembly. This assembly
     * is retrieved from the test's private storage directory.
     * 
     * At the moment, the returned array contains a single key 'routingPlan', which contains
     * a reference on the Plan object to be used to rule the test session.
     * 
     * @return array
     */
    protected function getAssembly()
    {
        if (is_null($this->assembly)) {
            $compiledTest = $this->getRequestParameter('Compilation');
            $fileName = $this->getDirectory($compiledTest)->getPath() . TestAssembler::ASSEMBLY_FILENAME;
            $this->assembly = include $fileName;
        }
        
        return $this->assembly;
    }
    
    /**
     * Get the Plan object describing the test plan to be taken.
     * 
     * @return \oat\irtTest\model\routing\Plan
     */
    protected function getRoutingPlan()
    {
        $model = $this->getAssembly();
        return $model['routingPlan'];
    }
    
    /**
     * Get the Route object providing the next items to be taken by the candidate,
     * with respect to the Plan.
     * 
     * @return \oat\irtTest\model\routing\Route
     */
    protected function getRoute()
    {
        if (is_null($this->route)) {
            $plan = $this->getRoutingPlan();
            $state = $this->getCachedState();
            if (isset($state['route'])) {
                $this->route = $plan->restoreRoute($state['route']);
            } else {
                $this->route = $plan->instantiateRoute();
            }
        }
        return $this->route;
    }
    
    /**
     * Get the score to the last item taken by the candidate.
     * 
     * @return string
     */
    protected function getLastScore()
    {
        $state = $this->getCachedState();
        $testServiceCallId = $this->getServiceCallId();
        $itemId = $state['current'];
        $resultServer = taoResultServer_models_classes_ResultServerStateFull::singleton();
        $var = $resultServer->getVariable("${testServiceCallId}.${itemId}", 'SCORE');
        
        return $var[0]->variable->getValue();
    }
    
    /**
     * Get the response to the last item taken by the candidate.
     * 
     * @return string
     */
    protected function getLastResponse()
    {
        $state = $this->getCachedState();
        $testServiceCallId = $this->getServiceCallId();
        $itemId = $state['current'];
        $resultServer = taoResultServer_models_classes_ResultServerStateFull::singleton();
        $var = $resultServer->getVariable("${testServiceCallId}.${itemId}", 'RESPONSE');
        
        return $var[0]->variable->getValue();
    }
    
    /**
     * Get a reference on the array representing the state of the test.
     * 
     * The returned array contains the following keys:
     * 
     * * 'current' : The identifier of the current item (not set if no item already taken).
     * * 'candidateId' : The identifier of the candidate.
     * * 'sessionId' : The identifier of the test session. 
     * 
     * @return array
     */
    protected function &getCachedState()
    {
        if (is_null($this->state)) {
            $this->state = json_decode($this->getState(), true);
            
            if ($this->state === null) {
                $this->state = array();
            }
        }
        return $this->state;
    }
    
    /**
     * Update the state and commit the changes into the persistent state
     * storage of TAO.
     */
    protected function updateState()
    {
        $this->state['route'] = $this->getRoutingPlan()->persistRoute($this->getRoute());
        $this->setState(json_encode($this->state));
    }
    
    /**
     * The index action is the entry point for the candidate to take its test. It must not be
     * invoked through XHR calls.
     */
    public function index()
    {
        $state = &$this->getCachedState();
        
        if (isset($state['current'])) {
            // We have a current item (candidate pressed F5 or comes back on
            // the test after a break).
            $itemId = $state['current'];
        } else {
            // No current item, let's retrieve the very first item
            // of the route.
            $session = common_session_SessionManager::getSession();
            $state['candidateId'] = $session->getUserUri();
            $state['sessionId'] = $this->getServiceCallId();
            
            $itemId = $this->getRoute()->getNextItem($state['sessionId'], $state['candidateId']);
            $state['current'] = $itemId;
        }

        $this->updateState();
        
        // Gather data useful to build the view.
        $testServiceCallId = $this->getServiceCallId();
        $testDefinitionUri = $this->getRequestParameter('Test');
        $testCompilationUri = $this->getRequestParameter('Compilation');

        // Transmit data to view.
        $this->setData('client_config_url', $this->getClientConfigUrl());
        
        $this->setData('test_context', TestContext::buildContext(
            $this->getRoutingPlan(),
            $itemId,
            $testServiceCallId,
            $testDefinitionUri,
            $testCompilationUri)
        );
        
        $this->setView('test_runner.tpl', 'irtTest');
    }
    
    /**
     * The next action is used to update the candidate's client through AJAX calls, in order
     * to deliver to him the next item in the Route, with respect to the current Plan.
     * 
     * This method must always be called through XHR.
     */
    public function next()
    {
        $state = &$this->getCachedState();
        
        $sessionId = $this->getServiceCallId();
        $lastScore = $this->getLastScore();
        $lastResponse = $this->getLastResponse();
        $lastId = $state['current'];
        
        $itemId = $this->getRoute()->getNextItem(
            $state['sessionId'],
            $state['candidateId'],
            $lastId,
            $lastResponse,
            $lastScore
        );
        
        $state['current'] = $itemId;
        $this->updateState();
        
        $testServiceCallId = $this->getServiceCallId();
        $testDefinitionUri = $this->getRequestParameter('Test');
        $testCompilationUri = $this->getRequestParameter('Compilation');
        
        $testContext = TestContext::buildContext(
            $this->getRoutingPlan(),
            $itemId,
            $testServiceCallId,
            $testDefinitionUri,
            $testCompilationUri
        );
        
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($testContext, JSON_HEX_QUOT | JSON_HEX_APOS);
    }
}
