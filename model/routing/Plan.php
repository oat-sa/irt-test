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

namespace oat\irtTest\model\routing;

use oat\oatbox\PhpSerializable;

/**
 * A Plan describes how a IRT Test will take place. An implementation
 * of Plan will take care of the Route instantiation logic, and set up
 * all additional logic a specific IRT test needs to be taken.
 * 
 * @author Joel Bout <joel@taotesting.com>
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @see Route The Route abstract class.
 */
interface Plan extends PhpSerializable {
    /**
     * Instantiate a Route object to be used to determine
     * which item is the next one in the item flow.
     * 
     * @return Route
     */
    public function instantiateRoute();
    
    /**
     * Restore a Route object from a string representation of its state.
     * 
     * @param string $stateString The string defining the intrinsic state of the Route object to be restored.
     * @return Route
     */
    public function restoreRoute($stateString);
}