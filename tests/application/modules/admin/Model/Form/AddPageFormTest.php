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
 *
 * @author yugeon
 */
namespace test\App\Admin\Model\Form;

use \App\Admin\Model\Form\AddPageForm,
 \Mockery as m;

class AddPageFormTest extends \PHPUnit_Framework_TestCase
{

    protected $object;

    public function setUp()
    {
        $this->object = new AddPageForm;
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

    public function testInit()
    {
        $this->assertArrayHasKey('label', $this->object->getElements());
        $this->assertArrayHasKey('title', $this->object->getElements());
        $this->assertArrayHasKey('class', $this->object->getElements());
        $this->assertArrayHasKey('target', $this->object->getElements());
        $this->assertArrayHasKey('order', $this->object->getElements());
        $this->assertArrayHasKey('type', $this->object->getElements());
        $this->assertArrayHasKey('module', $this->object->getElements());
        $this->assertArrayHasKey('controller', $this->object->getElements());
        $this->assertArrayHasKey('action', $this->object->getElements());
        $this->assertArrayHasKey('params', $this->object->getElements());
        $this->assertArrayHasKey('route', $this->object->getElements());
        $this->assertArrayHasKey('resetParams', $this->object->getElements());
        $this->assertArrayHasKey('uri', $this->object->getElements());
        $this->assertArrayHasKey('isActive', $this->object->getElements());
        $this->assertArrayHasKey('isVisible', $this->object->getElements());
        $this->assertArrayHasKey('resource', $this->object->getElements());
        $this->assertArrayHasKey('privilege', $this->object->getElements());
        $this->assertArrayHasKey('submit', $this->object->getElements());
        $this->assertArrayHasKey('reset', $this->object->getElements());
        //$this->assertArrayHasKey('captcha', $this->object->getElements());
    }

    public function testFormName()
    {
        $this->assertEquals('addPage', $this->object->getName());
    }

    public function testMustBePost()
    {
        $this->assertEquals('post', $this->object->getMethod());
    }

}
