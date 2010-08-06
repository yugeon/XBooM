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

    public function testGetContainer()
    {
        $container = $this->application->getBootstrap()->getContainer();
        if (isset($this->_options['DIContainer']))
        {
            $this->assertTrue(
                    $this->_myBootstrap->getContainerFactory()
                    instanceof $this->_options['DIContainer']['factoryClass']);
            $this->assertTrue($container instanceof sfServiceContainerInterface);
        }
        else
        {
            $this->assertTrue($container instanceof Zend_Registry);
        }
    }

}