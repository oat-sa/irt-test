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

namespace oat\irtTest\model\routing\simple;

use oat\irtTest\model\routing\Route as RouteInterface;
use oat\irtTest\model\routing\Plan;

/**
 * Implementations of this class will contain the logic of obtaining the next item to be 
 * presented to the candidate, with respect to a given Plan.
 * 
 * @author Joel Bout <joel@taotesting.com>
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @see Plan The Plan interface.
 */
abstract class Route implements RouteInterface {
    
    /**
     * Create a new Route object.
     * 
     * @param Plan $plan The Plan the Route must consider to deliver an approriate flow of items.
     */
    public function __construct(Plan $plan) {
        $this->setPlan($plan);
    }
    
    /**
     * Get the Plan to be respected by the Route.
     * 
     * @return Plan
     */
    protected function getPlan() {
        return $this->plan;
    }
    
    /**
     * Set the Plan to be respected by the Route.
     * 
     * @param Plan $plan
     */
    protected function setPlan(Plan $plan) {
        $this->plan = $plan;
    }    
}