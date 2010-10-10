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
 * Functional test for navigation service.
 *
 * @author yugeon
 */
namespace test\Xboom\Model\Service;
use \Xboom\Model\Domain\Navigation\Menu,
    \Xboom\Model\Domain\Navigation\Page,
    \Xboom\Model\Domain\Acl\Resource,
    \Xboom\Model\Domain\Acl\Permission,
    \Xboom\Acl\Acl;

/**
 * @group functional
 */
class NavigationFunctionalTest extends \FunctionalTestCase
{

    protected $navigationName = 'default';
    protected $navigationService;
    protected $page1;

    public function setUp()
    {
        parent::setUp();

        $this->navigationService = $this->_sc->getService('NavigationService');

        $newsResource = new Resource();
        $newsResource->name = 'News';
        $this->_em->persist($newsResource);

        $viewPermission = 'view';

        $this->page1 = new Page();
        $this->page1->label = 'Page 1';
        $this->page1->title = 'Home page';
        $this->page1->type  = 'mvc';
        $this->page1->module = 'core';
        $this->page1->controller = 'index';
        $this->page1->action = 'index';
        $this->page1->resource = $newsResource;
        $this->page1->privilege = $viewPermission;
        $this->_em->persist($this->page1);

        $page2 = new Page();
        $page2->label = 'Page 1.1';
        $page2->order = 2;
        $page2->type  = 'mvc';
        $page2->module = 'core';
        $page2->controller = 'news';
        $page2->action = 'index';
        $this->_em->persist($page2);

        $page3 = new Page();
        $page3->label = 'Page 1.1.1';
        $page3->type  = 'uri';
        $page3->uri = 'http://framework.zend.com';
        $this->_em->persist($page3);

        $page4 = new Page();
        $page4->label = 'Page 2';
        $page4->title = 'Search engine';
        $page4->type  = 'uri';
        $page4->uri = 'http://google.com';
        $this->_em->persist($page4);

        $page5 = new Page();
        $page5->label = 'Page 1.2';
        $page5->order = 1;
        $page5->type  = 'mvc';
        $page5->module = 'core';
        $page5->controller = 'news';
        $page5->action = 'last';
        $this->_em->persist($page5);

        $this->page1->addChildPage($page2);
        $this->page1->addChildPage($page5);
        $page2->addChildPage($page3);


        $menu = new Menu();
        $menu->name = $this->navigationName;
        $menu->assignToPage($this->page1);
        $menu->assignToPage($page4);
        $this->_em->persist($menu);

        $this->_em->flush();
    }

    public function testCanCreateService()
    {
        $this->assertNotNull($this->navigationService);
    }

    public function testGetNavigationByName()
    {
        $actual = array();
        $expected = array(
            'Page 1',
                'Page 1.2',
                'Page 1.1',
                    'Page 1.1.1',
            'Page 2'
        );
        $nav = $this->navigationService->getNavigation($this->navigationName);

        $iterator = new \RecursiveIteratorIterator($nav,
            \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $page) {
            $actual[] = $page->getLabel();
        }
        $this->assertEquals($expected, $actual);
    }

    public function testSuccessfulAccessToSecurePage()
    {
        $acl = new Acl();
        $acl->addRole('guest');
        $acl->addResource('News');
        $acl->allow('guest', 'News', 'view');

        $nav = $this->navigationService->getNavigation($this->navigationName);

        $view = \Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
        $navHelper = $view->navigation($nav);
        $navHelper->setAcl($acl);
        $navHelper->setRole('guest');

        $front = \Zend_Controller_Front::getInstance();
        $router = $front->getRouter();
        $router->addDefaultRoutes();

//        $expected = array(
//            'Page 1',
//                'Page 1.2',
//                'Page 1.1',
//                    'Page 1.1.1',
//            'Page 2'
//        );
        $actual = $navHelper->menu()->render();

        $this->assertContains('Page 1', $actual);
        $this->assertContains('Page 1.2', $actual);
        $this->assertContains('Page 1.1', $actual);
        $this->assertContains('Page 1.1.1', $actual);
        $this->assertContains('Page 2', $actual);
    }

    public function testDeniedAccessToSecurePage()
    {
        $acl = new Acl();
        $acl->addRole('guest');
        $acl->addResource('News');
        $acl->deny('guest', 'News', 'view');

        $nav = $this->navigationService->getNavigation($this->navigationName);

        $view = \Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
        $navHelper = $view->navigation($nav);
        $navHelper->setAcl($acl);
        $navHelper->setRole('guest');

        $front = \Zend_Controller_Front::getInstance();
        $router = $front->getRouter();
        $router->addDefaultRoutes();

//        $expected = array(
//            'Page 1',
//                'Page 1.2',
//                'Page 1.1',
//                    'Page 1.1.1',
//            'Page 2'
//        );
        $actual = $navHelper->menu()->render();

        $this->assertNotContains('Page 1', $actual);
        $this->assertNotContains('Page 1.2', $actual);
        $this->assertNotContains('Page 1.1', $actual);
        $this->assertNotContains('Page 1.1.1', $actual);
        $this->assertContains('Page 2', $actual);
    }

    public function testCanAddOneAndSamePageToMultipleMenus()
    {
        $adminMenu = new Menu();
        $adminMenu->name = 'admin';
        $adminMenu->assignToPage($this->page1);
        $this->_em->persist($adminMenu);
        $this->_em->flush();


    }
}
