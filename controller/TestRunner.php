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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */

namespace oat\irtTest\controller;

use tao_actions_ServiceModule;
use oat\irtRandomTest\model\ChaosEngine;
use oat\irtTest\model\TestAssembler;
use oat\irtTest\model\routing\Plan;
use oat\irtTest\model\routing\Route;

/**
 * The TestRunner is the controller dedicated to deliver an IRT Test
 * to a given candidate.
 * 
 * The index action is used as a bootstrap, when the TestRunner is called as a service. On the
 * other hand, the next action is called within an Ajax context to update the candidate's client
 * when the next item has to be delivered.
 * 
 * @author Joel Bout <joel@taotesting.com>
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 */
class TestRunner extends tao_actions_ServiceModule {
    private $model = null;
    
    private $state = null;
    
    private $route = null;
    
    protected function getTestModel() {
        if (is_null($this->model)) {
            $compiledTest = $this->getRequestParameter('Compilation');
            $fileName = $this->getDirectory($compiledTest)->getPath() . TestAssembler::ASSEMBLY_FILENAME;
            $this->model = include $fileName;
        }
        return $this->model;
    }
    
    /**
     * @return Plan
     */
    protected function getRoutingPlan() {
        $model = $this->getTestModel();
        return $model['routingPlan'];
    }
    
    /**
     * @return Route
     */
    protected function getRoute() {
        if (is_null($this->route)) {
            $plan = $this->getRoutingPlan();
            $state = $this->getState();
            if (isset($state['route'])) {
                $this->route = $plan->restoreRoute($state['route']);
            } else {
                $this->route = $plan->instantiateRoute();
            }
        }
        return $this->route;
    }
    
    protected function getCachedState() {
        if (is_null($this->state)) {
            $this->state = json_decode($this->getState(), true);
        }
        return $this->state;
    }
    
    protected function updateState() {
        $this->state['route'] = $this->route->getStateString();
        $this->setState(json_encode($this->state));
    }
    
    public function index() {
        // called as service
        
        $state = $this->getState();
        
        // if we have a current item (example)
        if (isset($state['sm']['current'])) {
            $itemUri = $state['sm']['current'];
        } else {
            $itemUri = $this->getRoute()->getNextItem(null);
            // @todo update state with new current item
        } 
        
        $this->updateState();
        
        // @todo render item
    }
    
    public function next() {
        
        // ajax call
        // parameters? testServiceCallId, testdefinition, lastItemServiceCallId
        
        // @todo get last score
        $lastScore = 0;
        
        // get routing Engine
        $itemUri = $this->getRoute()->getNextItem($lastScore);
        
        if (empty($itemUri)) {
            // @todo end test
        } else {
            // @todo update state with current item
            $this->updateState();
            // @todo give test info required to render item
        }
    }
    
}