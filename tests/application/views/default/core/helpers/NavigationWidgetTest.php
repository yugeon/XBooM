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
 * Description of MenuWidgetTest
 *
 * @author yugeon
 */

namespace test\Core\View\Helper;

require_once APPLICATION_PATH . '/views/default/core/helpers/NavigationWidget.php';

use \Mockery as m;

class NavigationWidgetTest extends \PHPUnit_Framework_TestCase
{

    protected $object;
    protected $navHelper;

    public function setUp()
    {
        $this->object = new \Core_View_Helper_NavigationWidget;

        $userIdentity = m::mock('stdClass');
        $userIdentity->shouldReceive('getRoleId')->andReturn(array('1', '2'));
        $userIdentity->shouldReceive('getRoles')->andReturn(array('1', '2'));
        
        $authService = m::mock('AuthService');
        $authService->shouldReceive('getCurrentUserIdentity')->andReturn($userIdentity);

        $acl = m::mock('Zend_Acl');

        $aclService = m::mock('AclService');
        $aclService->shouldReceive('getAcl')->andReturn($acl);

        $navContainer = m::mock('Zend_Navigation_Container');
        $navigationService = m::mock('NavigationService');
        $navigationService->shouldReceive('getNavigation')->andReturn($navContainer);

//        $authService->shouldReceive('getForm')->andReturn($form);

        $serviceContainer = m::mock('ServiceContainer');
        $serviceContainer->shouldReceive('getService')->with('AuthService')->andReturn($authService);
        $serviceContainer->shouldReceive('getService')->with('AclService')->andReturn($aclService);
        $serviceContainer->shouldReceive('getService')->with('NavigationService')->andReturn($navigationService);

        $this->navHelper = m::mock('Zend_View_Helper_Navigation_Helper');
        $this->navHelper->shouldReceive('setAcl')->andReturn($this->navHelper);
        $this->navHelper->shouldReceive('setRole')->andReturn($this->navHelper);

        $view = m::mock('Zend_View_Interface');
        $view->shouldReceive('__isset')->andReturn(true);
        $view->shouldReceive('navigation')->andReturn($this->navHelper);

        $view->serviceContainer = $serviceContainer;

        $this->object->setView($view);
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

    public function testShouldInitNavigationHelper()
    {
        $this->assertEquals($this->object, $this->object->navigationWidget());
        $this->assertEquals($this->navHelper, $this->object->getNavigationHelper());
    }

    public function testShouldDelegateCallsToNavigationHelper()
    {
        $this->assertNotNull($this->object->menu());
        $this->assertNotNull($this->object->breadcrumbs());
    }
}
