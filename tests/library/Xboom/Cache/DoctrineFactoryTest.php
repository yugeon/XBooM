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
        $this->markTestSkipped();
        $arrayCache = Xboom_Cache_DoctrineFactory::getCache('ArrayCache');
        $this->assertType('Doctrine\\Common\Cache\\ArrayCache', $arrayCache);
    }
}
