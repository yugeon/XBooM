<?php
/**
 *  CMF for web applications based on Zend Framework 1 and Doctrine 2
 *  Copyright (C) 2010  Eugene Gruzdev aka yugeon
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright  Copyright (c) 2010 yugeon <yugeon.ru@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html  GNU GPLv3
 */

/**
 * Description of LoginUserTest
 *
 * @author yugeon
 */

namespace test\Core\View\Helper;

require_once APPLICATION_PATH . '/views/default/core/helpers/LoginWidget.php';

use \Mockery as m;

class LoginWidgetTest extends \PHPUnit_Framework_TestCase
{

    protected $object;
    protected $authService;
    protected $view;

    public function setUp()
    {
        $this->object = new \Core_View_Helper_LoginWidget;

        $userIdentity = m::mock('stdClass');
        //$userIdentity->shouldR

        $form = m::mock('Zend_Form');
        $form->shouldReceive('setAction');

        $this->authService = m::mock('AuthService');
        $this->authService->shouldReceive('getCurrentUserIdentity')->andReturn($userIdentity);
        $this->authService->shouldReceive('getForm')->andReturn($form);

        $serviceContainer = m::mock('ServiceContainer');
        $serviceContainer->shouldReceive('getService')->andReturn($this->authService);

        $this->view = m::mock('Zend_View_Interface');
        $this->view->shouldReceive('__isset')->andReturn(true);
        $this->view->shouldReceive('url');
        $this->view->serviceContainer = $serviceContainer;

        $this->object->setView($this->view);
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }


    public function testCanCreateTestObject()
    {
        $this->assertNotNull($this->object);
    }

    public function testShouldExtendZendViewHelperInterface()
    {
        $this->assertType('Zend_View_Helper_Interface', $this->object);
    }

    public function testCanSetRenderScriptPath()
    {
        $scriptPath = 'auth/login.phtml';
        $this->object = new \Core_View_Helper_LoginWidget($scriptPath);
        $this->assertEquals($scriptPath, $this->object->getScriptPath());
    }

    public function testIfCurrentUserDoesNotHaveIdentityReturnLoginForm()
    {
        $expected = 'html text with login form';
        $this->authService->shouldReceive('hasIdentity')->andReturn(false);
        $this->view->shouldReceive('render')->andReturn($expected);
        $this->assertEquals($expected, $this->object->loginWidget());
    }

    public function testIfCurrentUserHasIdentityReturnIdentity()
    {
        $expected = 'html text with identity';
        $this->authService->shouldReceive('hasIdentity')->andReturn(true);
        $this->view->shouldReceive('render')->andReturn($expected);
        $this->assertEquals($expected, $this->object->loginWidget());
    }
}
