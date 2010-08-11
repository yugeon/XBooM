<?php
require_once 'PHPUnit/Framework.php';
/**
 * Description of DoctrineFactoryTest
 *
 * @author yugeon
 */
class DoctrineFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var Xboom_Cache_DoctrineFactory
     */
    protected $object = null;

    public function testGetArrayCache()
    {
        //$this->markTestSkipped();
        $arrayCache = Xboom_Cache_DoctrineFactory::getCache('Doctrine\Common\Cache\ArrayCache');
        $this->assertType('Doctrine\\Common\Cache\\ArrayCache', $arrayCache);
    }
    public function testGetApcCache()
    {
        //$this->markTestSkipped();
        $apcCache = Xboom_Cache_DoctrineFactory::getCache('Doctrine\Common\Cache\ApcCache');
        $this->assertType('Doctrine\\Common\Cache\\ApcCache', $apcCache);
    }
    public function testGetZendCache()
    {
        $options = array('frontendOptions' => array(),
                         'backendOptions'  => array()
            );
        $zendCache = Xboom_Cache_DoctrineFactory::getCache('Xboom_Cache_DoctrineAdapter', $options);
        $this->assertType('Xboom_Cache_DoctrineAdapter', $zendCache);
    }
}
