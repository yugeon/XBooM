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
        $sc = $this->object->makeContainer();
        $sc->addParameters(array(
            'mailer.username' => 'foo',
            'mailer.password' => 'bar',
            'mailer.class' => 'Zend_Mail',
        ));
        $mailer = $sc->mailer;
        $mailerTransport = $mailer->getDefaultTransport();

        $this->assertTrue($sc instanceof sfServiceContainerInterface);
        $this->assertTrue($mailer instanceof  Zend_Mail);
        $this->assertTrue($mailerTransport instanceof  Zend_Mail_Transport_Smtp);
    }

    public  function testMakeContainerFromDump()
    {
        $this->object->setOptions(array('enableDumpFile' => '1'));

        $sc = $this->object->makeContainer();
        $sc->addParameters(array(
            'mailer.username' => 'foo',
            'mailer.password' => 'bar',
            'mailer.class' => 'Zend_Mail',
        ));
        $mailer = $sc->mailer;
        $mailerTransport = $mailer->getDefaultTransport();

        $this->assertTrue($mailer instanceof  Zend_Mail);
        $this->assertTrue($mailerTransport instanceof  Zend_Mail_Transport_Smtp);
    }

    public function testDIContainerAsNativeForZF()
    {
        $resourceName = 'TestResource';
        $resource = new Zend_Acl();

        $sc = $this->object->makeContainer();
        $sc->{$resourceName} = $resource;

        $this->assertTrue($sc->{$resourceName} instanceof  Zend_Acl);
    }
}