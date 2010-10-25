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
 * Description of MenuTest
 *
 * @author yugeon
 */

namespace test\App\Core\Model\Domain\Navigation;

use \App\Core\Model\Domain\Navigation\Menu,
 \Mockery as m;

class MenuTest extends \PHPUnit_Framework_TestCase
{

    protected $object;

    public function setUp()
    {
        $this->object = new Menu;
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
        $pages = array(
            m::mock('Page'),
            m::mock('Page'),
            m::mock('Page'),
        );

        $properties = array(
            'id' => 1,
            'name' => 'default',
            'description' => 'This is default menu',
            'pages' => $pages
        );

        foreach ($properties as $key => $value)
        {
            $mutator = 'set' . \ucfirst($key);
            $accessor = 'get' . \ucfirst($key);
            $this->assertEquals($this->object, $this->object->{$mutator}($value));
            $this->assertEquals($value, $this->object->{$accessor}());
        }
    }

    public function testCanAssignPage()
    {
        $page = m::mock('Page');
        $this->object->assignToPage($page);
        $this->assertContains($page, $this->object->getPages());
    }

    public function testCanAddMenu()
    {
        $menuData1 = array(
            'name' => 'menu name',
            'description' => 'ffdf',
            'noise' => 'laldjf'
        );

        $menuData2 = $menuData1;
        unset($menuData2['description']);

        $this->assertEquals($this->object, $this->object->add($menuData1));
        $this->assertEquals($this->object, $this->object->add($menuData2));
    }

    public function testCanRemoveChildPage()
    {
        $page = m::mock('Page');
        $this->object->assignToPage($page);
        $this->assertEquals($this->object, $this->object->removePage($page));
        $this->assertNotContains($page, $this->object->getPages());
    }
}
