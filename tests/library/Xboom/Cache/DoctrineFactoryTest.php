<?php

/**
 *  CMF for web applications based on Zend Framework 1 and Doctrine 2
 *  Copyright (C) 2010  Eugene Gruzdev aka yugeon
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright  Copyright (c) 2010 yugeon <yugeon.ru@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html  GNU GPLv3
 */

/**
 * Description of DoctrineFactoryTest
 *
 * @author yugeon
 */
class Xboom_Cache_DoctrineFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var Xboom_Cache_DoctrineFactory
     */
    protected $object = null;

    public function testGetArrayCache()
    {
        $arrayCache = Xboom\Cache\DoctrineFactory::getCache('Doctrine\\Common\\Cache\\ArrayCache');
        $this->assertType('Doctrine\\Common\Cache\\ArrayCache', $arrayCache);
    }
    public function testGetApcCache()
    {
        $apcCache = Xboom\Cache\DoctrineFactory::getCache('Doctrine\\Common\\Cache\\ApcCache');
        $this->assertType('Doctrine\\Common\Cache\\ApcCache', $apcCache);
    }
    public function testGetXCache()
    {
        $xCache = Xboom\Cache\DoctrineFactory::getCache('Doctrine\\Common\\Cache\\XcacheCache');
        $this->assertType('Doctrine\\Common\Cache\\XcacheCache', $xCache);
    }
    public function testGetZendCache()
    {
        $options = array('frontendOptions' => array(),
                         'backendOptions'  => array()
            );
        $zendCache = Xboom\Cache\DoctrineFactory::getCache('Xboom\\Cache\\DoctrineAdapter', $options);
        $this->assertType('Xboom\Cache\DoctrineAdapter', $zendCache);
    }
    public function testGetMemcache()
    {
        if (!extension_loaded('memcache'))
        {
            $this->assertTrue(TRUE, 'Memcache extenstion is not available.');
            return;
        }
        $options = array(
            'memcache' => array('host' => 'localhost', 'port' => 11211)
            );
        $memCache = Xboom\Cache\DoctrineFactory::getCache('Doctrine\\Common\\Cache\\MemcacheCache', $options);
        $this->assertType('Doctrine\\Common\\Cache\\MemcacheCache', $memCache);
    }
}
