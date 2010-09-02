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

require_once 'PHPUnit/Framework.php';
/**
 * Description of MediatorTest
 *
 * @author yugeon
 */
use \Mockery as m;
use Xboom\Model\Form\Mediator;

class MediatorTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var Xboom_Model_Form_Mediator
     */
    protected $object = null;

    /**
     *
     * @var RegisterUserForm
     */
    protected $userForm;

    /**
     *
     * @var RegisterUserValidator
     */
    protected $userValidator;

    /**
     *
     * @var array
     */
    protected $userData;

    public function setUp()
    {
        parent::setUp();

        $this->object = new Mediator('RegisterUser');

        $this->userForm = m::mock('\\Core\\Model\\Form\\RegisterUserForm');
        $this->userValidator = m::mock('\\Core\Model\\Domain\\Validator\RegisterUserValidator');

        $testUserName = 'TestUserName' . rand(1, 100);
        $testUserPassword = md5($testUserName);
        $userData = array(
            'login' => $testUserName,
            'name' => $testUserName,
            'password' => $testUserPassword,
            'confirm_password' => $testUserPassword,
        );

        $this->userData = $userData;
    }

    public function tearDown()
    {
        parent::tearDown();

        m::close();
    }

    public function testCanCreateMediatorWithName()
    {
        $this->assertNotNull($this->object);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShouldRaiseExceptionIfNameNotPass()
    {
        $this->object = new Mediator(new stdClass());
    }

    public function testGetForm()
    {
        $this->assertNotNull($this->object->getForm());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShouldRaiseExceptionIfFormNotExist()
    {
        $this->object->setName('NotExistForm');
        $this->assertNotNull($this->object->getForm());
    }

    public function testSetForm()
    {
        $this->object->setForm($this->userForm);
        $this->assertEquals($this->userForm, $this->object->getForm());
    }

    public function testGetValidator()
    {
        $this->assertNotNull($this->object->getValidator());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShouldRaiseExceptionIfValidatorNotExist()
    {
        $this->object->setName('NotExistValidator');
        $this->assertNotNull($this->object->getValidator());
    }

    public function testValidateWhenFormIsValidAndDataIsValid()
    {
        // inject mock form
        $this->userForm->shouldReceive('isValid')->with($this->userData)->andReturn(true);
        $this->object->setForm($this->userForm);

        // inject mock user validator
        $this->userValidator->shouldReceive('isValid')->andReturn(true);
        $this->userValidator->shouldReceive('getPropertiesForValidation')->andReturn(array());
        $this->object->setValidator($this->userValidator);

        // not break validation if form is not valid
        $this->assertTrue($this->object->isValid($this->userData, false));
    }

    public function testValidateWhenFormIsNotValidAndDataIsValid()
    {
        // inject mock form
        $this->userForm->shouldReceive('isValid')->with($this->userData)->andReturn(false);
        $this->object->setForm($this->userForm);

        // inject mock user validator
        $this->userValidator->shouldReceive('isValid')->andReturn(true);
        $this->userValidator->shouldReceive('getPropertiesForValidation')->andReturn(array());
        $this->object->setValidator($this->userValidator);

        // not break validation if form is not valid
        $this->assertFalse($this->object->isValid($this->userData, false));
    }

    public function testValidateWhenFormIsValidAndDataIsNotValid()
    {
        // inject mock form
        $this->userForm->shouldReceive('isValid')->with($this->userData)->andReturn(true);
        $this->userForm->shouldReceive('getElements')->andReturn(array());
        $this->object->setForm($this->userForm);

        // inject mock user validator
        $this->userValidator->shouldReceive('isValid')->andReturn(false);
        $this->userValidator->shouldReceive('getMessages')->andReturn(array());
        $this->userValidator->shouldReceive('getPropertiesForValidation')->andReturn(array());
        $this->object->setValidator($this->userValidator);

        // not break validation if form is not valid
        $this->assertFalse($this->object->isValid($this->userData, false));
    }

    public function testValidateWhenFormIsNotValidAndDataIsNotValid()
    {
        // inject mock form
        $this->userForm->shouldReceive('isValid')->with($this->userData)->andReturn(false);
        $this->userForm->shouldReceive('getElements')->andReturn(array());
        $this->object->setForm($this->userForm);

        // inject mock user validator
        $this->userValidator->shouldReceive('isValid')->andReturn(false);
        $this->userValidator->shouldReceive('getPropertiesForValidation')->andReturn(array());
        $this->userValidator->shouldReceive('getMessages')->andReturn(array());
        $this->object->setValidator($this->userValidator);

        // not break validation if form is not valid
        $this->assertFalse($this->object->isValid($this->userData, false));
    }

    public function testGetValues()
    {
        $expected = $this->userData;
        unset($expected['confirm_password']);

        // inject mock user validator
        $this->userValidator->shouldReceive('isValid')->andReturn(true);

        $elementValidator1 = m::mock('\\Xboom\\Model\\Validate\\Element\\BaseValidator');
        $elementValidator2 = m::mock('\\Xboom\\Model\\Validate\\Element\\BaseValidator');
        $elementValidator3 = m::mock('\\Xboom\\Model\\Validate\\Element\\BaseValidator');

        $elementValidator1->shouldReceive('getValue')->andReturn($expected['login']);
        $elementValidator2->shouldReceive('getValue')->andReturn($expected['name']);
        $elementValidator3->shouldReceive('getValue')->andReturn($expected['password']);

        $this->userValidator->shouldReceive('getPropertiesForValidation')
                            ->andReturn(array(
                                'login'      => $elementValidator1,
                                'name'     => $elementValidator2,
                                'password'  => $elementValidator3
                                ));
        $this->object->setValidator($this->userValidator);

        $this->object->isValid($this->userData);
        $this->assertSame($expected, $this->object->getValues());
    }
}
