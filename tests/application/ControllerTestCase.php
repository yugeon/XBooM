<?php

/**
 * Description of ControllerTestCase
 * @author yugeon
 */
require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';
use \App\Core\Model\Domain\UserIdentity,
    \Mockery as m;
class ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{
    /**
     *
     * @var Zend_Application
     */
    protected $application;

    public function setUp()
    {
        $this->bootstrap = array($this, 'appBootstrap');
        parent::setUp();
        $this->application->bootstrap();
        $bootsrap = $this->application->getBootstrap();
        $front = $bootsrap->getResource('FrontController');
        $front->setParam('bootstrap', $bootsrap);

        $this->_setUpServiceLayer();
    }

    public function appBootstrap()
    {
        $this->application = new Zend_Application(
                APPLICATION_ENV,
                APPLICATION_PATH . '/configs/application.ini');
    }

    public function _setUpServiceLayer()
    {
        $sc = $this->application->getBootstrap()->getContainer();
        $realAuthService = $sc->getService('AuthService');
        $sc->setService('RealAuthService', $realAuthService);
        $authService = m::mock($realAuthService);
        $authService->shouldReceive('getCurrentUserIdentity')->andReturn(new UserIdentity());
        $sc->setService('AuthService', $authService);
    }
}