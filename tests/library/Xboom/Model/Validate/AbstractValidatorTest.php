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

use Xboom\Model\Validate\Element\ValidatorInterface as ElementValidator;
use Xboom\Model\Validate\AbstractValidator;
use \Mockery as m;

class TestModelValidator extends AbstractValidator
{

    protected static $testValidator;

    public static function setTestValidator($validator)
    {
        self::$testValidator = $validator;
    }

    public function init()
    {
        $propertyName = 'setInInitMethod';
        $this->addPropertyValidator($propertyName, self::$testValidator);
    }

}

/**
 * Description of AbstractTest
 *
 * @author yugeon
 */
class Xboom_Model_Validate_AbstractTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var TestModelValidator
     */
    protected $object;
    protected $loginValidator;

    public function setUp()
    {
        parent::setUp();

        $this->loginValidator = m::mock('Xboom\\Model\\Validate\\Element\\ValidatorInterface');
        $this->loginValidator->shouldReceive('isValid')->andReturn(true);
        $this->loginValidator->shouldReceive('getMessages')->andReturn(array());
        TestModelValidator::setTestValidator($this->loginValidator);
        $this->object = new TestModelValidator();
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testSetAndGetPropertyValidator()
    {
        $propertyName = 'login';
        $this->object->addPropertyValidator($propertyName, $this->loginValidator);

        $this->assertEquals(
                $this->loginValidator,
                $this->object->getPropertyValidator($propertyName)
        );
    }

    public function testMethodInitMustCall()
    {
        $this->assertEquals(
                $this->loginValidator,
                $this->object->getPropertyValidator('setInInitMethod')
        );
    }

    /**
     * @expectedException \Xboom\Model\Validate\NoSuchPropertyException
     */
    public function testRaiseExceptionInRequestNotSettedValidator()
    {
        $this->object->getPropertyValidator('InvalidProperty');
    }

    public function testValidDataMustReturnTrue()
    {
        $validData = array(
            'login' => 'validLogin',
            'password' => 'validPassword',
            'email' => 'valid@email.com'
        );
        $this->object->addPropertyValidator('login', $this->loginValidator);

        $this->assertTrue($this->object->isValid($validData));
    }

    public function testInvalidDataMustReturnFalse()
    {
        $invalidData = array(
            'login' => 'invalidLogin',
            'password' => 'validPassword',
            'email' => 'valid@email.com'
        );

        $loginValidator = m::mock('Xboom\\Model\\Validate\\Element\\ValidatorInterface');
        $loginValidator->shouldReceive('isValid')
                ->with($invalidData['login'])->andReturn(false);
        $this->object->addPropertyValidator('login', $loginValidator);
        $this->assertFalse($this->object->isValid($invalidData));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRaiseExceptionWhenNotArrayValidate()
    {
        $dataForValidation = new stdClass();
        $this->object->isValid($dataForValidation);
    }

    public function testGetMessageMustBeEmptyIfDataIsValid()
    {
        $validData = array(
            'login' => 'validLogin',
            'password' => 'validPassword',
            'email' => 'valid@email.com'
        );
        $this->loginValidator->shouldReceive('getMessages')->andReturn(array());
        $this->object->addPropertyValidator('login', $this->loginValidator);

        $this->assertEquals(0, count($this->object->getMessages()));
    }

    public function testGetMessageMustReturnArrayOfMessagesIfDataIsNotValid()
    {
        $invalidData = array(
            'login' => 'invalidLogin',
            'password' => 'validPassword',
            'email' => 'valid@email.com'
        );
        $errorMsg = 'Login must be valid!';

        $loginValidator = m::mock('Xboom\\Model\\Validate\\Element\\ValidatorInterface');
        $loginValidator->shouldReceive('isValid')
                ->with($invalidData['login'])->andReturn(false);
        $loginValidator->shouldReceive('getMessages')->andReturn(array($errorMsg));
        $this->object->addPropertyValidator('login', $loginValidator);
        $this->assertFalse($this->object->isValid($invalidData));
        $messages = $this->object->getMessages();
        $this->assertContains($errorMsg, $messages['login']);
    }

    public function testRequiredElement()
    {
        $validDataWithoutRequiredElement = array(
            //'login'     => 'validLogin',
            'password'  => 'validPassword',
            'email'     => 'valid@email.com'
        );
        $required = true;
        $errorMsg = TestModelValidator::REQUIRED_ELEMENT;
        $loginValidator = m::mock('Xboom\\Model\\Validate\\Element\\ValidatorInterface');
        $loginValidator->shouldReceive('addErrorMessage');
        $loginValidator->shouldReceive('getMessages')->andReturn(array($errorMsg));
        $this->object->addPropertyValidator('login', $loginValidator, $required);

        $this->assertFalse($this->object->isValid($validDataWithoutRequiredElement));

        $messages = $this->object->getMessages();
        $this->assertContains($errorMsg, $messages['login']);
    }

    public function testShouldReturnFilteredValues()
    {
        $unfilteredData = array(
            'login' => 'invalidLogin<br>',
            //'password' => '  validPassword  ',
        );

        $filteredData = array(
            'login' => 'invalidLogin',
           // 'password' => 'validPassword',
        );

        $expectedData = array(
            'setInInitMethod' => 'invalidLogin',
            'login' => 'invalidLogin',
        );

        //$loginValidator = m::mock('Xboom\\Model\\Validate\\Element\\ValidatorInterface');
        $this->loginValidator->shouldReceive('isValid')->andReturn(true);
        $this->loginValidator->shouldReceive('getValue')->andReturn($filteredData['login']);

        $this->object->addPropertyValidator('login', $this->loginValidator);

        $this->object->isValid($unfilteredData);

        $this->assertEquals($expectedData, $this->object->getValues());
    }

}
