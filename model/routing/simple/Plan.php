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

use oat\irtTest\model\routing\Plan as PlanInterface;
use oat\irtTest\model\routing\Route;
use tao_models_classes_service_FileStorage;

/**
 * An abstract implementation of Plan relying on a given File Storage.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class Plan implements PlanInterface
{
    private $storage;
    
    public function __construct(tao_models_classes_service_FileStorage $storage)
    {
        $this->setStorage($storage);
    }
    
    protected function getStorage()
    {
        return $this->storage;
    }
    
    protected function setStorage(tao_models_classes_service_FileStorage $storage)
    {
        $this->storage = $storage;
    }
}