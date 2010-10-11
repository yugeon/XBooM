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

        $this->userForm = m::mock('Zend_Form');
        $this->userValidator = m::mock('\\Xboom\\Model\\Validate\\ValidatorInteface');
        $this->userModel = m::mock('\\Xboom\\Model\\Domain\\DomainObject');
        $this->userModel->shouldReceive('getValidator')->andReturn($this->userValidator);
        $this->userModel->shouldReceive('setData')->andReturn()->mock();

        $this->object = new Mediator($this->userForm, $this->userModel);

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

    public function testCanCreateMediator()
    {
        $this->assertNotNull($this->object);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShouldRaiseExceptionIfFormOrModelNotPass()
    {
        $this->object = new Mediator(null, null);
    }

    public function testGetForm()
    {
        $this->assertNotNull($this->object->getForm());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShouldRaiseExceptionIfModelNotExist()
    {
        $this->object->setModel(null);
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

    public function testValidateWhenFormIsNotValidAndDoBreak()
    {
        $this->userForm->shouldReceive('isValid')->with($this->userData)->andReturn(false);
        $this->userForm->shouldReceive('getValues')->andReturn($this->userData);

        // not break validation if form is not valid
        $this->assertFalse($this->object->isValid($this->userData));
    }

    public function testValidateWhenFormIsValidAndDataIsValid()
    {
        $this->userForm->shouldReceive('isValid')->with($this->userData)->andReturn(true);
        $this->userForm->shouldReceive('getValues');

        $this->userValidator->shouldReceive('isValid')->andReturn(true);
        $this->userValidator->shouldReceive('getPropertiesForValidation')->andReturn(array());
        $this->userValidator->shouldReceive('getValues')->andReturn(array());

        // not break validation if form is not valid
        $this->assertTrue($this->object->isValid($this->userData, false));
    }

    public function testValidateWhenFormIsNotValidAndDataIsValid()
    {
        $this->userForm->shouldReceive('isValid')->with($this->userData)->andReturn(false);
        $this->userForm->shouldReceive('getValues');

        $this->userValidator->shouldReceive('isValid')->andReturn(true);
        $this->userValidator->shouldReceive('getPropertiesForValidation')->andReturn(array());
        $this->userValidator->shouldReceive('getValues')->andReturn(array());

        // not break validation if form is not valid
        $this->assertFalse($this->object->isValid($this->userData, false));
    }

    public function testValidateWhenFormIsValidAndDataIsNotValid()
    {
        $this->userForm->shouldReceive('isValid')->with($this->userData)->andReturn(true);
        $this->userForm->shouldReceive('getElements')->andReturn(array());
        $this->userForm->shouldReceive('getValues');

        $this->userValidator->shouldReceive('isValid')->andReturn(false);
        $this->userValidator->shouldReceive('getMessages')->andReturn(array());
        $this->userValidator->shouldReceive('getPropertiesForValidation')->andReturn(array());
        $this->userValidator->shouldReceive('getValues')->andReturn(array());

        // not break validation if form is not valid
        $this->assertFalse($this->object->isValid($this->userData, false));
    }

    public function testValidateWhenFormIsNotValidAndDataIsNotValid()
    {
        $this->userForm->shouldReceive('isValid')->with($this->userData)->andReturn(false);
        $this->userForm->shouldReceive('getElements')->andReturn(array());
        $this->userForm->shouldReceive('getValues');

        $this->userValidator->shouldReceive('isValid')->andReturn(false);
        $this->userValidator->shouldReceive('getPropertiesForValidation')->andReturn(array());
        $this->userValidator->shouldReceive('getValues')->andReturn(array());
        $this->userValidator->shouldReceive('getMessages')->andReturn(array());

        // not break validation if form is not valid
        $this->assertFalse($this->object->isValid($this->userData, false));
    }

    public function testGetValues()
    {
        $expected = $this->userData;

        $this->userForm->shouldReceive('isValid')->with($this->userData)->andReturn(true);
        $this->userForm->shouldReceive('getElements')->andReturn(array());
        $this->userForm->shouldReceive('getValues');


        $this->userValidator->shouldReceive('isValid')->andReturn(true);
        $this->userValidator->shouldReceive('getValues')->andReturn(array());

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

        $this->object->isValid($this->userData);
        $this->assertSame($expected, $this->object->getValues());
    }

    public function testGetValidModel()
    {
        $this->assertNotNull($this->object->getModel());
    }

    public function testGetSetDomainValidator()
    {
        $testDomainValidator = m::mock('\\Xboom\\Model\\Validate\ValidatorInterface');
        $this->assertEquals($this->object, $this->object->setDomainValidator($testDomainValidator));
        $this->assertEquals($testDomainValidator, $this->object->getDomainValidator());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShouldRaiseExceptionIfValidatorIncorrectObject()
    {
        $incorrectValidator = new stdClass();
        $this->object->setDomainValidator($incorrectValidator);
    }

    public function testValidateWhenDomainValidatorExistsAndDataIsValid()
    {
        $this->userForm->shouldReceive('isValid')->with($this->userData)->andReturn(true);
        $this->userForm->shouldReceive('getValues');
        $this->userValidator->shouldReceive('isValid')->andReturn(true);
        $this->userValidator->shouldReceive('getPropertiesForValidation')->andReturn(array());
        $this->userValidator->shouldReceive('getValues')->andReturn(array());

        $domainValidator = m::mock('\\Xboom\\Model\\Validate\\ValidatorInterface');
        $domainValidator->shouldReceive('isValid')->andReturn(true);
        $domainValidator->shouldReceive('getValues')->andReturn(array());

        $this->object->setDomainValidator($domainValidator);

        $this->assertTrue($this->object->isValid($this->userData, false));
    }

    public function testValidateWhenDomainValidatorExistsAndDataIsNotValid()
    {
        $this->userForm->shouldReceive('isValid')->with($this->userData)->andReturn(true);
        $this->userForm->shouldReceive('getElements')->andReturn(array());
        $this->userForm->shouldReceive('getValues');
        $this->userValidator->shouldReceive('isValid')->andReturn(true);
        $this->userValidator->shouldReceive('getMessages')->andReturn(array());
        $this->userValidator->shouldReceive('getPropertiesForValidation')->andReturn(array());
        $this->userValidator->shouldReceive('getValues')->andReturn(array());

        $domainValidator = m::mock('\\Xboom\\Model\\Validate\\ValidatorInterface');
        $domainValidator->shouldReceive('isValid')->andReturn(false);
        $domainValidator->shouldReceive('getValues')->andReturn(array());
        $domainValidator->shouldReceive('getMessages')->andReturn(array());

        $this->object->setDomainValidator($domainValidator);

        $this->assertFalse($this->object->isValid($this->userData, false));
    }

    public function testDataShouldPassByChainFiltersFromValidatorsVariousLevels()
    {
        $data = array(
            'name' => 'badName <br />',
            'name2' => ' goodName '
        );
        
        $filteredData = array(
            'name' => 'badName',
            'name2' => 'goodName'
        );

        $this->userForm->shouldReceive('isValid')->with($data)->andReturn(true);
        $this->userForm->shouldReceive('getElements')->andReturn(array());
        $this->userForm->shouldReceive('getValues')->andReturn($data);
        $this->userValidator->shouldReceive('isValid')->andReturn(true);
        $this->userValidator->shouldReceive('getMessages')->andReturn(array());
        $this->userValidator->shouldReceive('getPropertiesForValidation')->andReturn(array());
        $this->userValidator->shouldReceive('getValues')->andReturn($filteredData);


        $domainValidator = m::mock('\\Xboom\\Model\\Validate\\ValidatorInterface');
        $domainValidator->shouldReceive('isValid')->with($filteredData)->andReturn(true);
        $domainValidator->shouldReceive('isValid')->with($data)->andReturn(false);
        $domainValidator->shouldReceive('getValues')->andReturn($filteredData);
        $domainValidator->shouldReceive('getMessages')->andReturn(array());

        $this->object->setDomainValidator($domainValidator);
        
        $this->assertTrue($this->object->isValid($data, false));
    }
}
