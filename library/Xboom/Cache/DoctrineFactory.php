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

namespace Xboom\Cache;
/**
 * Factory for Doctrine cache driver.
 *
 * @author yugeon
 */
class DoctrineFactory
{
    /**
     * Return configured cache driver.
     *
     * @param string $cacheDriver
     * @param array $options
     * @return Doctrine\Common\Cache\Cache
     */
    public static function getCache($cacheDriver, $options = array())
    {
        switch ($cacheDriver)
        {
            case 'Xboom\\Cache\\DoctrineAdapter':
                $zendCache = \Zend_Cache::factory('Core', 'File',
                        $options['frontendOptions'], $options['backendOptions']);
                $_cacheDriver = new DoctrineAdapter($zendCache);
                break;
            case 'Doctrine\\Common\\Cache\\Memcache':
                $memcache = new \Memcache;
                $memcache->connect($options['memcache']['host'], $options['memcache']['port']);
                $_cacheDriver = new \Doctrine\Common\Cache\MemcacheCache;
                $_cacheDriver->setMemcache($memcache);
                break;
            default :
                    $_cacheDriver = new $cacheDriver();
        }
        if (! ($_cacheDriver instanceof \Doctrine\Common\Cache\Cache))
        {
            echo $_cacheDriver;
            throw new \Xboom\Exception ('Cache driver must be an instance of Doctrine\\Common\\Cache\\Cache interface.');
            return null;
        }
        return $_cacheDriver;
    }
}
