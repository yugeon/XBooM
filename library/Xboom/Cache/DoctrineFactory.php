<?php

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
