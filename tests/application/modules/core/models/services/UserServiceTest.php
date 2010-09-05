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

namespace test\Core\Model\Service;

use \Mockery as m,
    Core\Model\Service\UserService,
    Core\Model\Form\RegisterUserForm,
    Core\Model\Domain\Validator\RegisterUserValidator;

require_once 'PHPUnit/Framework.php';
require_once 'Mockery.php';

/**
 * Description of User
 *
 * @author yugeon
 */
class UserTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @var App_Service_User
     */
    protected $object = null;
    /**
     *
     * @var \Doctrine\ORM\EntityManager 
     */
    protected $em;

    /**
     *
     * @var RegisterUserForm
     */
    protected $userMediator;


    /**
     *
     * @var \Xboom\Model\Domain\AbstractObject
     */
    protected $userModel;

    /**
     *
     * @var array
     */
    protected $userData;

    public function setUp()
    {
        parent::setUp();
        $this->em = m::mock('\\Doctrine\\ORM\\EntityManager');
        $this->em->shouldReceive('persist');
        $this->em->shouldReceive('flush');

        $this->userMediator = m::mock('\\Xboom\Model\\Form\\Mediator');

        $this->userModel = m::mock('\\Xboom\\Model\\Domain\\AbstractObject');

        $this->object = new UserService($this->em);

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

    // ---------------------------
    // Test UserService Operations
    // ---------------------------

    public function testGetUserById()
    {
        $this->em->shouldReceive('find')->once()->andReturn($this->userModel);

        $user = $this->object->getUserById(1);
        $this->assertEquals($this->userModel, $user);
    }

    public function testGetUsersList()
    {
        $result = array(
            $this->userModel,
            $this->userModel,
        );

        $this->em->shouldReceive('createQuery')->once()->andReturn($this->em);
        $this->em->shouldReceive('getResult')->once()->andReturn($result);

        $userList = $this->object->getUsersList();
        $this->assertTrue(is_array($userList));
        $this->assertEquals(2, count($userList));
    }

    public function testRegisterNewUser()
    {
        $this->userMediator->shouldReceive('isValid')->andReturn(true);
        $this->userMediator->shouldReceive('getValidModel')->andReturn($this->userModel);

        $this->object->setFormToModelMediator('RegisterUser', $this->userMediator);

        $this->userModel = $this->object->registerUser($this->userData);

        $this->assertNotNull($this->userModel);
    }
    
    /**
     * @expectedException \Xboom\Model\Service\Exception
     */
    public function testShouldRaiseExceptionIfCannotRegisterUserBecouseUserDataInvalid()
    {
        // inject mock mediator
        $this->userMediator->shouldReceive('isValid')->andReturn(false);
        $this->object->setFormToModelMediator('RegisterUser', $this->userMediator);

        $user = $this->object->registerUser($this->userData);
    }
//
//    public function _testUserFormMustContainAllErrorsIfCannotRegisterUser()
//    {
//        // inject mock form
//        $this->userMediator->shouldReceive('isValid')->andReturn(true);
//        $this->userMediator->shouldReceive('getValues')->andReturn($this->userData);
//        $this->userMediator->shouldReceive('getElements')->andReturn(array());
//        $this->userMediator->shouldReceive('getMessages');
//        $this->object->setForm('RegisterUser', $this->userMediator);
//
//        // inject mock user validator
//        $this->userValidator->shouldReceive('isValid')->andReturn(false);
//        $this->userValidator->shouldReceive('getMessages')->andReturn(array('error1', 'error2'));
//        $this->object->setValidator('RegisterUser', $this->userValidator);
//
//        try
//        {
//            $user = $this->object->registerUser($this->userData);
//        }
//        catch (\Xboom\Service\Exception $e)
//        {
//            $this->assertEquals(2, count($this->object->getForm('RegisterUser')->getMessages()) );
//        }
//    }
//
//    public function _testIfUserFormIsInvalidThenMustReturnUserFormWithErrors()
//    {
//        $testUserName = 'TestUserName' . rand(1, 100);
//        $testUserPassword = '123456';
//        $testConfirmUserPassword = '654321';
//        $userData = array(
//            'login' => $testUserName,
//            'name' => $testUserName,
//            'password' => $testUserPassword,
//            'confirm_password' => $testConfirmUserPassword,
//        );
//
//        try
//        {
//            $user = $this->object->registerUser($userData);
//            $this->fail('Must raise exception');
//        }
//        catch (\Xboom\Service\Exception $e)
//        {
//            $user = null;
//        }
//
//        $form = $this->object->getForm('RegisterUser');
//
//        $this->assertNull($user);
//        $this->assertNotNull($form);
//        $this->assertTrue($form->getMessages() > 0);
//    }
//
//
//    public function _testGetValidator()
//    {
//        $this->assertNotNull($this->object->getValidator('RegisterUser'));
//    }
//
//    /**
//     * @expectedException \InvalidArgumentException
//     */
//    public function _testShouldRaiseExceptionIfValidatorNotExist()
//    {
//        $this->assertNotNull($this->object->getValidator('NotExistValidator'));
//    }
}
