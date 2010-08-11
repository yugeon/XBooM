<?php

require_once 'PHPUnit/Framework.php';

class Xboom_Application_Resource_DIContainerFactoryTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Xboom_Application_Resource_DIContainerFactory
     */
    protected $object;
    protected $options;

    protected function setUp()
    {
        parent::setUp();
        $config = new Zend_Config_Ini(
                        APPLICATION_PATH . '/configs/application.ini',
                        APPLICATION_ENV);
        $this->options = $config->toArray();
        $this->object = new Xboom_Application_Resource_DIContainerFactory(
                        $this->options['DIContainer']['params']);
    }

    protected function tearDown()
    {
        parent::tearDown();
        $file = $this->options['DIContainer']['params']['dumpFilePath'];
        if (file_exists($file))
        {
            unlink($file);
        }
    }

    public function testMakeContainer()
    {
        // example from
        // http://components.symfony-project.org/dependency-injection/trunk/book/04-Builder
        $this->object->setOptions(array('enableAutogenerateDumpFile' => '0'));
        $sc = $this->object->makeContainer();
        $sc->addParameters(array(
            'mailer.username' => 'foo',
            'mailer.password' => 'bar',
            'mailer.class' => 'Zend_Mail',
        ));
        $mailer = $sc->mailer;
        $mailerTransport = $mailer->getDefaultTransport();

        $this->assertType('sfServiceContainerInterface', $sc);
        $this->assertType('Zend_Mail', $mailer);
        $this->assertType('Zend_Mail_Transport_Smtp', $mailerTransport);
    }

    public function testMakeContainerAutogenerateDumpFile()
    {
        $filename = $this->options['DIContainer']['params']['dumpFilePath'];
        if (file_exists($filename))
        {
            unlink($filename);
        }

        $this->object->setOptions(array('enableAutogenerateDumpFile' => '1'));
        $sc = $this->object->makeContainer();
        $sc->addParameters(array(
            'mailer.username' => 'foo',
            'mailer.password' => 'bar',
            'mailer.class' => 'Zend_Mail',
        ));
        $mailer = $sc->mailer;
        $mailerTransport = $mailer->getDefaultTransport();

        $this->assertType('Zend_Mail', $mailer);
        $this->assertType('Zend_Mail_Transport_Smtp', $mailerTransport);

        $this->assertTrue(file_exists($filename));
    }

    public function testDIContainerAsNativeForZF()
    {
        $resourceName = 'TestResource';
        $resource = new Zend_Acl();

        $sc = $this->object->makeContainer();
        $sc->{$resourceName} = $resource;

        $this->assertType('Zend_Acl', $sc->{$resourceName});
    }
}