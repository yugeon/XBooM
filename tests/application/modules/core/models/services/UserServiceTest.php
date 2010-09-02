<?php

use \Mockery as m;
use Core\Model\Form\RegisterUserForm;
use Core\Model\Domain\Validator\RegisterUserValidator;

require_once 'PHPUnit/Framework.php';
require_once 'Mockery.php';

/**
 * Description of User
 *
 * @author yugeon
 */
class App_Service_UserTest extends PHPUnit_Framework_TestCase
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
        $this->em = m::mock('\\Doctrine\\ORM\\EntityManager');
        $this->em->shouldReceive('persist');
        $this->em->shouldReceive('flush');

        $this->userMediator = m::mock('\\Xboom\Model\\Form\\Mediator');
        
        $this->object = new Core\Service\UserService($this->em);

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

    public function teardown()
    {
        parent::tearDown();
        m::close();
    }

    public function testGetUsersList()
    {
        $result = array(
            new Core\Model\Domain\User(),
            new Core\Model\Domain\User()
        );

        $this->em->shouldReceive('createQuery')->once()->andReturn($this->em);
        $this->em->shouldReceive('getResult')->once()->andReturn($result);

        $userList = $this->object->getUsersList();
        $this->assertTrue(is_array($userList));
        $this->assertEquals(2, count($userList));
    }

    public function testGetUserById()
    {
        $testUserName = 'TestUserName';
        $result = new Core\Model\Domain\User(array('login' => $testUserName));

        $this->em->shouldReceive('find')->once()->andReturn($result);

        $user = $this->object->getUserById(1);
        $this->assertEquals($result, $user);
    }

    public function testRegisterNewUser()
    {
        // inject mediator
        $validData = $this->userData;
        unset($validData['confirm_password']);
        $this->userMediator->shouldReceive('isValid')->andReturn(true);
        $this->userMediator->shouldReceive('getValues')->andReturn($validData);
        $this->object->setFormMediator('RegisterUser', $this->userMediator);

        $user = $this->object->registerUser($this->userData);

        $this->assertNotNull($user);
        $this->assertEquals($this->userData['login'], $user->login);
        $this->assertEquals($this->userData['password'], $user->password);
    }

    /**
     * @expectedException \Xboom\Service\Exception
     */
    public function testShouldRaiseExceptionIfCannotRegisterUserBecouseUserDataInvalid()
    {
        // inject mock mediator
        $this->userMediator->shouldReceive('isValid')->andReturn(false);
        $this->object->setFormMediator('RegisterUser', $this->userMediator);

        $user = $this->object->registerUser($this->userData);
    }

    public function _testUserFormMustContainAllErrorsIfCannotRegisterUser()
    {
        // inject mock form
        $this->userMediator->shouldReceive('isValid')->andReturn(true);
        $this->userMediator->shouldReceive('getValues')->andReturn($this->userData);
        $this->userMediator->shouldReceive('getElements')->andReturn(array());
        $this->userMediator->shouldReceive('getMessages');
        $this->object->setForm('RegisterUser', $this->userMediator);

        // inject mock user validator
        $this->userValidator->shouldReceive('isValid')->andReturn(false);
        $this->userValidator->shouldReceive('getMessages')->andReturn(array('error1', 'error2'));
        $this->object->setValidator('RegisterUser', $this->userValidator);

        try
        {
            $user = $this->object->registerUser($this->userData);
        }
        catch (\Xboom\Service\Exception $e)
        {
            $this->assertEquals(2, count($this->object->getForm('RegisterUser')->getMessages()) );
        }
    }

    public function _testIfUserFormIsInvalidThenMustReturnUserFormWithErrors()
    {
        $testUserName = 'TestUserName' . rand(1, 100);
        $testUserPassword = '123456';
        $testConfirmUserPassword = '654321';
        $userData = array(
            'login' => $testUserName,
            'name' => $testUserName,
            'password' => $testUserPassword,
            'confirm_password' => $testConfirmUserPassword,
        );

        try
        {
            $user = $this->object->registerUser($userData);
            $this->fail('Must raise exception');
        }
        catch (\Xboom\Service\Exception $e)
        {
            $user = null;
        }

        $form = $this->object->getForm('RegisterUser');

        $this->assertNull($user);
        $this->assertNotNull($form);
        $this->assertTrue($form->getMessages() > 0);
    }

    public function testGetForm()
    {
        $this->assertNotNull($this->object->getForm('RegisterUser'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShouldRaiseExceptionIfFormNotExist()
    {
        $this->assertNotNull($this->object->getForm('NotExistForm'));
    }

    public function _testSetForm()
    {
        $formName = 'RegisterUser';
        $this->object->setForm($formName, $this->userMediator);
        $this->assertEquals($this->userMediator, $this->object->getForm($formName));
    }

    public function _testGetValidator()
    {
        $this->assertNotNull($this->object->getValidator('RegisterUser'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function _testShouldRaiseExceptionIfValidatorNotExist()
    {
        $this->assertNotNull($this->object->getValidator('NotExistValidator'));
    }
}
