<?php

use \Mockery as m;

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

    public function setUp()
    {
        parent::setUp();
        $this->em = m::mock('\\Doctrine\\ORM\\EntityManager');
        $this->object = new Core\Service\UserService($this->em);
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
        $testUserName = 'TestUserName' . rand(1, 100);
        $testUserPassword = md5($testUserName);
        $userData = array(
            'login' => $testUserName,
            'name' => $testUserName,
            'password' => $testUserPassword,
            'confirm_password' => $testUserPassword,
        );

        $this->em->shouldReceive('persist');
        $this->em->shouldReceive('flush');

        $user = $this->object->registerNewUser($userData);

        $this->assertNotNull($user);
        $this->assertEquals($userData['login'], $user->login);
        $this->assertEquals($userData['password'], $user->password);
    }

    public function testIfUserFormIsInvalidThenMustReturnUserFormWithErrors()
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
            $user = $this->object->registerNewUser($userData);
            $this->fail('Must raise exception');
        }
        catch (\Xboom\Exception $e)
        {
            $user = null;
        }

        $form = $this->object->getForm('RegisterNewUser');

        $this->assertNull($user);
        $this->assertNotNull($form);
        $this->assertTrue($form->getMessages() > 0);
    }

    public function testIfUserDataIsInvalidThenMustReturnUserFormWithErrors()
    {
        $testEmptyUserName = '';
        $testUserPassword = '12345';
        $userData = array(
            'login' => $testEmptyUserName,
            'name' => $testEmptyUserName,
            'password' => $testUserPassword,
            'confirm_password' => $testUserPassword,
        );

        try
        {
            $user = $this->object->registerNewUser($userData);
            $this->fail('Must raise exception');
        }
        catch (\Xboom\Exception $e)
        {
            $user = null;
        }

        $form = $this->object->getForm('RegisterNewUser');

        $this->assertNull($user);
        $this->assertNotNull($form);
        $this->assertTrue(count($form->getMessages()) > 0);
    }

    public function testGetForm()
    {
        $this->assertNotNull($this->object->getForm('RegisterNewUser'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShouldRaiseExceptionIfFormNotExist()
    {
        $this->assertNotNull($this->object->getForm('NotExistForm'));
    }
}
