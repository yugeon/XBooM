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

namespace test\Xboom\Model\Domain;

use Xboom\Model\Validate\ValidatorInterface,
    Xboom\Model\Domain\AbstractObject,
   \Mockery as m;

require_once 'PHPUnit/Framework.php';

class TestAbstractObject extends AbstractObject
{

    protected $testPropertyTrue = true;
    protected $testPropertyFalse = true;
    protected $testProperty;
    protected $_testPropertyTrue = true;
    protected $testProtectedProperty;
    protected $_testProtectedProperty;
    protected $login;
    protected $password;

    public function getTestPropertyFalse()
    {
        return false;
    }

    public function setTestProperty($value)
    {
        $this->testProperty = 'Set by mutator';
    }

}

class AbstractObjectTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Xboom_Model_Domain_AbstractObject
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new TestAbstractObject;
    }

    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testCanGetProtectedProperty()
    {
        $this->assertTrue($this->object->testPropertyTrue);
    }

    public function testCannotGetProtectedPropertyWithUnderscoreSuffix()
    {
        try
        {
            $this->assertTrue($this->object->_testPropertyTrue);
        }
        catch (\InvalidArgumentException $exc)
        {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testCannotGetNotExistenProperty()
    {
        try
        {
            $this->assertTrue($this->object->notExistenProperty);
        }
        catch (\InvalidArgumentException $exc)
        {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testCanGetProtectedPropertyByAccessorMethod()
    {
        $this->assertFalse($this->object->testPropertyFalse);
    }

    public function testCanSetProtectedProperty()
    {
        $testValue1 = 'New value';
        $this->object->testProtectedProperty = $testValue1;
        $this->assertEquals($testValue1, $this->object->testProtectedProperty);

        $testValue2 = 'Yet, another new value';
        $this->object->testProtectedProperty = $testValue2;
        $this->assertEquals($testValue2, $this->object->testProtectedProperty);
    }

    public function testCannotSetProtectedPropertyWithUnderscoreSuffix()
    {
        $testValue = 'test value';
        try
        {
            $this->object->_testProtectedProperty = $testValue;
        }
        catch (\InvalidArgumentException $exc)
        {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testCannotSetNotExistetProperty()
    {
        $testValue = 'test value';
        try
        {
            $this->object->testNotExistedProperty = $testValue;
        }
        catch (\InvalidArgumentException $exc)
        {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testCanSetProtectedPropertyByMutator()
    {
        $testValue = 'Test value';
        $this->object->testProperty = $testValue;
        $this->assertEquals('Set by mutator', $this->object->testProperty);
    }

    public function testCanGetProtectedPropertyByGetMethod()
    {
        $this->assertTrue($this->object->getTestPropertyTrue());
    }

    public function testCanSetProtectedPropertyByGetMethod()
    {
        $testValue1 = 'New value';
        $this->object->setTestProtectedProperty($testValue1);
        $this->assertEquals($testValue1, $this->object->getTestProtectedProperty());
    }

    public function testCannotGetProtectedPropertyWithUnderscoreSuffixByGetMethod()
    {
        try
        {
            $this->object->get_testPropertyTrue();
        }
        catch (\BadMethodCallException $exc)
        {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testCannontSetProtectedPropertyWithUnderscorePrefixBySetMethod()
    {
        $testValue = 'test value';
        try
        {
            $this->object->set_testProtectedProperty($testValue);
        }
        catch (\BadMethodCallException $exc)
        {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testGetAllPropertiesAsArray()
    {
        $expected = array(
            "testPropertyTrue" => true,
            "testPropertyFalse" => true,
            "testProperty" => null,
            "testProtectedProperty" => null,
            'login' => null,
            'password' => null,
        );
        $this->assertEquals($expected, $this->object->toArray());
    }

}