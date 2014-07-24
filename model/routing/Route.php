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

/**
 * Contains the logic of obtaining the next item to be presented to the candidate, by respecting
 * a given Plan. It also declares its state through the getStateString() method.
 *
 * @author Joel Bout <joel@taotesting.com>
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @see Plan The Plan interface.
 */
interface Route
{
    /**
     * Return the next item of the Route, or an empty string if the test is finished.
     * 
     * @param string $lastItemScore The score the candidate was granted against the last item he took. This parameter is optional if the candidate never took an item in this test before.
     * @return string $itemIdentifier The unique identifier of the next item to be delivered to the candidate.
     * @throws \oat\irtTest\model\RouteException If something goes wrong.
     */
    public function getNextItem($lastItemScore = '');
    
    /**
     * Return the serialized state of the Route, as a string.
     *
     * @return string
     * @throws \oat\irtTest\model\RouteException If something goes wrong.
     */
    public function getStateString();
}
