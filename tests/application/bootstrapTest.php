<?php

/**
 * Test case for bootstrap class
 *
 * @author yugeon
 */
class bootstrapTest extends ControllerTestCase
{

    protected $_options;
    protected $_myBootstrap;

    public function setUp()
    {
        parent::setUp();
        $this->_options = $this->application->getOptions();
        $this->_myBootstrap = $this->application->getBootstrap();
    }

    public function testGetContainerFactory()
    {
        $containerFactory = $this->_myBootstrap->getContainerFactory();
        if (isset($this->_options['DIContainer']))
        {
            $this->assertTrue(
                    $containerFactory instanceof $this->_options['DIContainer']['factoryClass']);
        }
        else
        {
            $this->assertNull($containerFactory);
        }
    }
    
    public function testGetContainer()
    {
        $container = $this->application->getBootstrap()->getContainer();
        if (isset($this->_options['DIContainer']))
        {
            $this->assertTrue($container instanceof sfServiceContainerInterface);
        }
        else
        {
            $this->assertTrue($container instanceof Zend_Registry);
        }
    }

    public function testGetDoctrineEntityManager()
    {
        $sc = $this->_myBootstrap->getContainer();
//        $sc->addParameters(array(
//            'doctrine.connection.options' => $this->_options['doctrine']['connection'],
//            'doctrine.orm.path_to_mappings' => $this->_options['doctrine']['pathToMappings'],
//            'doctrine.orm.path_to_proxies' => $this->_options['doctrine']['pathToProxies'],
//            'doctrine.orm.proxy_namespace' => $this->_options['doctrine']['proxiesNamespace'],
//        ));
        $em = $sc->getService('doctrine.orm.entitymanager');

        $this->assertTrue($em instanceof Doctrine\ORM\EntityManager);
    }
}