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

namespace test\Xboom\Model\Domain\Navigation;

use \Xboom\Model\Domain\Navigation\Page,
 \Mockery as m;

class PageTest extends \PHPUnit_Framework_TestCase
{

    protected $object;

    public function setUp()
    {
        $this->object = new Page;
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
        $resource = m::mock('Resource');
        $permission = m::mock('Permission');

        $properties = array(
            'id' => 1,
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
            'resource' => $resource,
            'permission' => $permission,
            'pages' => array()
        );

        foreach ($properties as $key => $value)
        {
            $mutator = 'set' . \ucfirst($key);
            $accessor = 'get' . \ucfirst($key);
            $this->assertEquals($this->object, $this->object->{$mutator}($value));
            $this->assertEquals($value, $this->object->{$accessor}());
        }
    }

}