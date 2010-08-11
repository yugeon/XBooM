<?php
/**
 * Factory for Doctrine cache driver.
 *
 * @author yugeon
 */
class Xboom_Cache_DoctrineFactory
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
            case 'Xboom_Cache_DoctrineAdapter':
                $zendCache = Zend_Cache::factory('Core', 'File',
                        $options['frontendOptions'], $options['backendOptions']);
                $_cacheDriver = new Xboom_Cache_DoctrineAdapter($zendCache);
                break;
            case 'Doctrine\\Common\\Cache\\Memcache':
            default :
                $_cacheDriver = new $cacheDriver();
        }
        if (! ($_cacheDriver instanceof Doctrine\Common\Cache\Cache))
        {
            throw new Xboom_Exception ('Cache driver must be an instance of Doctrine\\Common\\Cache\\Cache interface.');
            return null;
        }
        return $_cacheDriver;
    }
}
