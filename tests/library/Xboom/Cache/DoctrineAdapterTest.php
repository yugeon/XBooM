<?php

require_once 'PHPUnit/Framework.php';

/**
 * Description of DoctrineAdapterTest
 *
 * @author yugeon
 */
class Xboom_Cache_DoctrineAdapterTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var Xboom_Cache_DoctrineAdapter
     */
    protected $object;

    public function setUp()
    {
        parent::setUp();
        $config = new Zend_Config_Ini(
                        APPLICATION_PATH . '/configs/application.ini',
                        APPLICATION_ENV);
        $options = $config->toArray();
        $options = $options['doctrine']['cacheOptions'];
        $zendCache = Zend_Cache::factory('Core', 'File',
                $options['frontendOptions'], $options['backendOptions']);
        $this->object = new Xboom_Cache_DoctrineAdapter($zendCache);
    }

    public function testGetCache()
    {
        $this->assertType('Doctrine\\Common\\Cache\\Cache', $this->object);
    }

    public function testBasics()
    {
        $cache = $this->object;

        // Test save
        $cache->save('test_key', 'testing this out');

        // Test contains to test that save() worked
        $this->assertTrue($cache->contains('test_key'));

        // Test fetch
        $this->assertEquals('testing this out', $cache->fetch('test_key'));

        // Test delete
        $cache->save('test_key2', 'test2');
        $cache->delete('test_key2');
        $this->assertFalse($cache->contains('test_key2'));
    }

    public function testDeleteAll()
    {
        $cache = $this->object;
        $cache->save('test_key1', '1');
        $cache->save('test_key2', '2');
        $cache->deleteAll();

        $this->assertFalse($cache->contains('test_key1'));
        $this->assertFalse($cache->contains('test_key2'));
    }

    public function testDeleteByRegex()
    {
        $cache = $this->object;
        $cache->save('test_key1', '1');
        $cache->save('test_key2', '2');
        $cache->deleteByRegex('/test_key[0-9]/');

        $this->assertFalse($cache->contains('test_key1'));
        $this->assertFalse($cache->contains('test_key2'));
    }

    public function testDeleteByPrefix()
    {
        $cache = $this->object;
        $cache->save('test_key1', '1');
        $cache->save('test_key2', '2');
        $cache->deleteByPrefix('test_key');

        $this->assertFalse($cache->contains('test_key1'));
        $this->assertFalse($cache->contains('test_key2'));
    }

    public function testDeleteBySuffix()
    {
        $cache = $this->object;
        $cache->save('1test_key', '1');
        $cache->save('2test_key', '2');
        $cache->deleteBySuffix('test_key');

        $this->assertFalse($cache->contains('1test_key'));
        $this->assertFalse($cache->contains('2test_key'));
    }

    public function testDeleteByWildcard()
    {
        $cache = $this->object;
        $cache->save('test_key1', '1');
        $cache->save('test_key2', '2');
        $cache->delete('test_key*');

        $this->assertFalse($cache->contains('test_key1'));
        $this->assertFalse($cache->contains('test_key2'));
    }

    public function testNamespace()
    {
        $cache = $this->object;
        $cache->setNamespace('test_');
        $cache->save('key1', 'test');
        $this->assertTrue($cache->contains('key1'));

        $ids = $cache->getIds();
        $this->assertTrue(in_array('test_key1', $ids));
    }
}
