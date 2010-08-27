<?php

require_once 'PHPUnit/Framework.php';

/**
 * Description of AutoloaderTest
 *
 * @author yugeon
 */
class AutoloaderTest extends PHPUnit_Framework_TestCase
{
    protected $loaders = null;
    protected $includePath;
    protected $autoloader;
    protected $error;


    public function setUp()
    {
        parent::setUp();

        // Store original autoloaders
        $this->loaders = spl_autoload_functions();
        if (!is_array($this->loaders)) {
            // spl_autoload_functions does not return empty array when no
            // autoloaders registered...
            $this->loaders = array();
        }

        // Store original include_path
        $this->includePath = get_include_path();

        Xboom\Loader\Autoloader::resetInstance();
        $this->autoloader = Xboom\Loader\Autoloader::getInstance();

        // initialize 'error' member for tests that utilize error handling
        $this->error = null;
    }

    public function tearDown()
    {
        parent::tearDown();
        
        // Restore original autoloaders
        $loaders = spl_autoload_functions();
        foreach ($loaders as $loader) {
            spl_autoload_unregister($loader);
        }

        foreach ($this->loaders as $loader) {
            spl_autoload_register($loader);
        }

        // Retore original include_path
        set_include_path($this->includePath);

        // Reset autoloader instance so it doesn't affect other tests
        Xboom\Loader\Autoloader::resetInstance();
    }
    
    public function testAutoloaderShouldBeSingleton()
    {
        $autoloader = Xboom\Loader\Autoloader::getInstance();
        $this->assertSame($this->autoloader, $autoloader);
    }

    public function testSingletonInstanceShouldAllowReset()
    {
        Xboom\Loader\Autoloader::resetInstance();
        $autoloader = Xboom\Loader\Autoloader::getInstance();
        $this->assertNotSame($this->autoloader, $autoloader);
    }

    public function testAutoloaderShouldRegisterItselfWithSplAutoloader()
    {
        $autoloaders = spl_autoload_functions();
        $found = false;
        foreach ($autoloaders as $loader) {
            if (is_array($loader)) {
                if (('autoload' == $loader[1]) && ($loader[0] === get_class($this->autoloader))) {
                    $found = true;
                    break;
                }
            }
        }
        $this->assertTrue($found, 'Autoloader instance not found in spl_autoload stack: ' . var_export($autoloaders, 1));
    }

    public function testZfNamespacesShouldBeRegisteredByDefault()
    {
        $namespaces = $this->autoloader->getRegisteredNamespaces();
        $this->assertContains('Zend', $namespaces);
        $this->assertContains('ZendX', $namespaces);
    }

    public function testAutoloaderShouldAllowRegisteringArbitraryNamespaces()
    {
        $this->autoloader->registerNamespace('Phly_');
        $namespaces = $this->autoloader->getRegisteredNamespaces();
        $this->assertContains('Phly', $namespaces);
    }

    public function testAutoloaderShouldAllowRegisteringMultipleNamespacesAtOnce()
    {
        $this->autoloader->registerNamespace(array('Phly_' => null, 'Solar_' => null));
        $namespaces = $this->autoloader->getRegisteredNamespaces();
        $this->assertContains('Phly', $namespaces);
        $this->assertContains('Solar', $namespaces);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRegisteringInvalidNamespaceSpecShouldRaiseException()
    {
        $o = new stdClass;
        $this->autoloader->registerNamespace($o);
    }

    public function testAutoloaderShouldAllowUnregisteringNamespaces()
    {
        $this->autoloader->unregisterNamespace('Zend_');
        $namespaces = $this->autoloader->getRegisteredNamespaces();
        $this->assertNotContains('Zend_', $namespaces);
    }

    public function testAutoloaderShouldAllowUnregisteringMultipleNamespacesAtOnce()
    {
        $this->autoloader->unregisterNamespace(array('Zend_', 'ZendX_'));
        $namespaces = $this->autoloader->getRegisteredNamespaces();
        $this->assertNotContains('Zend_', $namespaces);
        $this->assertNotContains('ZendX_', $namespaces);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUnregisteringInvalidNamespaceSpecShouldRaiseException()
    {
        $o = new stdClass;
        $this->autoloader->unregisterNamespace($o);
    }


    public function testAutoloadShouldReturnFalseWhenNamespaceIsNotRegistered()
    {
        $this->assertFalse(Xboom\Loader\Autoloader::autoload('Foo_Bar'));
    }

    public function testAutoloadShouldReturnFalseWhenNamespaceIsNotRegistered1()
    {
        $this->assertFalse(Xboom\Loader\Autoloader::autoload('Foo\\Bar'));
    }

    public function addTestIncludePath()
    {
        set_include_path(dirname(__FILE__) . '/_files/' . PATH_SEPARATOR . $this->includePath);
    }

    public function testAutoloadShouldReturnFalseWhenNamespaceIsNotRegisteredButClassfileExists()
    {
        $this->addTestIncludePath();
        $this->assertFalse(Xboom\Loader\Autoloader::autoload('XboomLoaderAutoloader_Foo'));
    }

    public function testAutoloadShouldLoadClassWhenNamespaceIsRegisteredAndClassfileExists()
    {
        $this->addTestIncludePath();
        $this->autoloader->registerNamespace('XboomLoaderAutoloader');
        $result = Xboom\Loader\Autoloader::autoload('XboomLoaderAutoloader_Foo');
        $this->assertTrue((bool)$result);
        $this->assertTrue(class_exists('XboomLoaderAutoloader_Foo', false));
    }

    public function testAutoloadShouldLoadClassWhenNamespaceIsRegisteredAndClassfileExists2()
    {
        $this->addTestIncludePath();
        $this->autoloader->registerNamespace('XboomLoaderAutoloader');
        $result = Xboom\Loader\Autoloader::autoload('XboomLoaderAutoloader\\Foo1');
        $this->assertTrue((bool)$result);
        $this->assertTrue(class_exists('XboomLoaderAutoloader\\Foo1', false));
    }
    public function testAutoloadShouldLoadClassWhenNamespaceIsRegistereAndNoIncludePath()
    {
        $this->autoloader->registerNamespace(
                'XboomLoaderAutoloader',
                dirname(__FILE__) . '/_files/XboomLoaderAutoloader');
        $result = Xboom\Loader\Autoloader::autoload('XboomLoaderAutoloader\\Foo2');
        $this->assertTrue((bool)$result);
        $this->assertTrue(class_exists('XboomLoaderAutoloader\\Foo2', false));
    }
    public function testAutoloadShouldLoadClassWhenNamespaceIsRegistereAndNoIncludePath2()
    {
        $this->autoloader->registerNamespace(
                'XboomLoader\\Autoloader',
                dirname(__FILE__) . '/_files/XboomLoaderAutoloader');
        $result = Xboom\Loader\Autoloader::autoload('XboomLoader\\Autoloader\\Foo3');
        $this->assertTrue((bool)$result);
        $this->assertTrue(class_exists('XboomLoader\\Autoloader\\Foo3', false));
    }
}