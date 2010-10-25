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
 * Description of PageTest
 *
 * @author yugeon
 */

namespace test\App\Core\Model\Domain\Navigation;

use \App\Core\Model\Domain\Navigation\Page,
 \Mockery as m;

class PageTest extends \PHPUnit_Framework_TestCase
{

    protected $object;
    protected $resource;
    protected $permission;
    protected $data;

    public function setUp()
    {
        $this->object = new Page;

        $this->resource = m::mock('Resource');
        $this->permission = m::mock('Permission');

        $this->data = array(
            'uri' => array(
                'label' => 'Home',
                'class' => 'menu-item',
                'title' => 'Home page',
                'target' => '_blank',
                'type' => 'uri',
                'order' => 1,
                'module' => 'core',
                'controller' => 'index',
                'action' => 'index',
                'params' => array('param1' => 'value1', 'param2' => 'value2'),
                'route' => null,
                'resetParams' => true,
                'uri' => 'http://google.com/?a=b',
                'isActive' => true,
                'isVisible' => true,
                'resource' => $this->resource,
                'privilege' => $this->permission,
                'pages' => array()
            ),
            'mvc' => array(
                'label' => 'Home',
                'class' => 'menu-item',
                'title' => 'Home page',
                'target' => '_blank',
                'type' => 'mvc',
                'order' => 1,
                'module' => 'core',
                'controller' => 'index',
                'action' => 'index',
                'params' => array('param1' => 'value1', 'param2' => 'value2'),
                'route' => null,
                'resetParams' => true,
                'uri' => 'http://google.com/?a=b',
                'isActive' => true,
                'isVisible' => true,
                'resource' => $this->resource,
                'privilege' => $this->permission,
                'pages' => array()
            ),
        );
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

    public function testCanGetSetProperties()
    {
        $properties = $this->data['mvc'];
        foreach ($properties as $key => $value)
        {
            $mutator = 'set' . \ucfirst($key);
            $accessor = 'get' . \ucfirst($key);
            $this->assertEquals($this->object, $this->object->{$mutator}($value));
            $this->assertEquals($value, $this->object->{$accessor}());
        }
    }

    public function testCanAddMvcPage()
    {
        $this->object->add($this->data['mvc']);
        $expected = $this->data['mvc'];
        $expected['id'] = null;
        $expected['uri'] = null;
        unset($expected['pages']);

        $actual = $this->object->toArray();
        unset($actual['pages']);
        unset($actual['parent']);
        
        $this->assertEquals($expected, $actual);
    }

    public function testCanAddUriPage()
    {
        $this->object->add($this->data['uri']);
        $expected = $this->data['uri'];
        $expected['id'] = null;
        $expected['action'] = null;
        $expected['controller'] = null;
        $expected['module'] = null;
        $expected['params'] = null;
        $expected['route'] = null;
        $expected['resetParams'] = true;
        unset($expected['pages']);

        $actual = $this->object->toArray();
        unset($actual['pages']);
        unset($actual['parent']);

        $this->assertEquals($expected, $actual);
    }

    public function testCanAddChildPage()
    {
        $page = m::mock('Page');
        $this->assertEquals($this->object, $this->object->addChildPage($page));
        $this->assertContains($page, $this->object->getPages());

    }

    public function testCanRemoveChildPage()
    {
        $page = m::mock('Page');
        $this->object->addChildPage($page);
        $this->assertEquals($this->object, $this->object->removeChild($page));
        $this->assertNotContains($page, $this->object->getPages());
    }

    public function testCanAssignToParent()
    {
        $parentPage = m::mock('Page');
        $parentPage->shouldReceive('addChildPage')->with($this->object)->andReturn($parentPage);
        $this->assertEquals($this->object, $this->object->assignToParent($parentPage));
    }

    public function testCanRemoveParentAssociation()
    {
        $parentPage = m::mock('Page');
        $parentPage->shouldReceive('addChildPage')->with($this->object)->andReturn($parentPage);
        $parentPage->shouldReceive('removeChild')->with($this->object)->andReturn($parentPage);
        $this->object->assignToParent($parentPage);

        $this->assertEquals($this->object, $this->object->removeParent());
        $this->assertNull($this->object->getParent());
    }

    public function testCanChangeParentPage()
    {
        $newParent = m::mock('Page');
        $newParent->shouldReceive('addChildPage')->with($this->object)->andReturn($newParent);

        $this->assertEquals($this->object, $this->object->changeParent($newParent));
        $this->assertEquals($newParent, $this->object->getParent());
    }

}