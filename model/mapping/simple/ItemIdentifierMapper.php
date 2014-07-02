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

namespace oat\irtTest\model\mapping\simple;

use core_kernel_classes_Resource;
use oat\irtTest\model\mapping\ItemIdentifierMapper as ItemIdentifierMapperInterface;

/**
 * A simple implementation of the ItemIdentifierMapper interface, based on
 * Item URIs.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @see http://en.wikipedia.org/wiki/Uniform_Resource_Identifier Information about URIs.
 *
 */
class ItemIdentifierMapper implements ItemIdentifierMapperInterface
{
    /**
     * Returns the URI of the given $item as its unique identifier.
     * 
     * @param string $identifier 
     * @return string A URI.
     * @see http://en.wikipedia.org/wiki/Uniform_Resource_Identifier Information about URIs.
     */
    public function map(core_kernel_classes_Resource $item)
    {
        return $item->getUri();
    }
}