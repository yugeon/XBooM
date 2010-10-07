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
    \Xboom\Model\Domain\Acl\Permission;

/**
 * @group functional
 */
class NavigationFunctionalTest extends \FunctionalTestCase
{

    protected $newsResource;
    protected $viewPermission;
    protected $navigationService;

    public function setUp()
    {
        parent::setUp();

        $this->navigationService = $this->_sc->getService('NavigationService');

        $newsResource = new Resource();
        $newsResource->name = 'News';
        $this->_em->persist($newsResource);

        $viewPermission = 'view';

        $page1 = new Page();
        $page1->label = 'Home';
        $page1->title = 'Home page';
        $page1->type  = 'mvc';
        $page1->module = 'core';
        $page1->controller = 'index';
        $page1->action = 'index';
        $page1->resource = $newsResource;
        $page1->permission = $viewPermission;
        $this->_em->persist($page1);

        $page2 = new Page();
        $page2->label = 'News';
        $page2->title = 'Last news';
        $page2->type  = 'mvc';
        $page2->module = 'core';
        $page2->controller = 'news';
        $page2->action = 'index';
        $this->_em->persist($page2);

        $page3 = new Page();
        $page3->label = 'Zend';
        $page3->title = 'Go to Zend Framework off site';
        $page3->type  = 'uri';
        $page3->uri = 'http://framework.zend.com';
        $this->_em->persist($page3);

        $page4 = new Page();
        $page4->label = 'Googl';
        $page4->title = 'Search engine';
        $page4->type  = 'uri';
        $page4->uri = 'http://google.com';
        $this->_em->persist($page4);

        $page5 = new Page();
        $page5->label = 'News';
        $page5->title = 'Last news';
        $page5->type  = 'mvc';
        $page5->module = 'core';
        $page5->controller = 'news';
        $page5->action = 'index';
        $this->_em->persist($page5);

        $page1->addChildPage($page2);
        $page1->addChildPage($page5);
        $page2->addChildPage($page3);


        $menu = new Menu();
        $menu->name = 'default';
        $menu->assignToPage($page1);
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
        $this->navigationService->getNavigation();
    }
}
