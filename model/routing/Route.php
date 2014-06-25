<?php
/*
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

namespace oat\irtTest\model\routing;

interface Route
{
    /**
     * Create a new route, or restore an existing route
     * 
     * @param Plan $plan
     * @param string $state
     */
    public function __construct(Plan $plan, $state);
    
    /**
     * Return the next item of the route, or none if done
     * 
     * @param string $lastItemScore
     * @return string $itemIdentifier
     */
    public function getNextItem($lastItemScore);
    
    /**
     * Return the serialized state of the route
     */
    public function getStateString();
}