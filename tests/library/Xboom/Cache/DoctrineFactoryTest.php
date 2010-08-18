<?php
require_once 'PHPUnit/Framework.php';
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
        $arrayCache = Xboom_Cache_DoctrineFactory::getCache('Doctrine\Common\Cache\ArrayCache');
        $this->assertType('Doctrine\\Common\Cache\\ArrayCache', $arrayCache);
    }
    public function testGetApcCache()
    {
        $apcCache = Xboom_Cache_DoctrineFactory::getCache('Doctrine\Common\Cache\ApcCache');
        $this->assertType('Doctrine\\Common\Cache\\ApcCache', $apcCache);
    }
    public function testGetXCache()
    {
        $xCache = Xboom_Cache_DoctrineFactory::getCache('Doctrine\Common\Cache\XcacheCache');
        $this->assertType('Doctrine\\Common\Cache\\XcacheCache', $xCache);
    }
    public function testGetZendCache()
    {
        $options = array('frontendOptions' => array(),
                         'backendOptions'  => array()
            );
        $zendCache = Xboom_Cache_DoctrineFactory::getCache('Xboom_Cache_DoctrineAdapter', $options);
        $this->assertType('Xboom_Cache_DoctrineAdapter', $zendCache);
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
        $memCache = Xboom_Cache_DoctrineFactory::getCache('Doctrine\\Common\\Cache\\MemcacheCache', $options);
        $this->assertType('Doctrine\\Common\\Cache\\MemcacheCache', $memCache);
    }
}
