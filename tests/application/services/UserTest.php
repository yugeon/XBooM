<?php
use \Mockery as m;

require_once 'PHPUnit/Framework.php';
require_once 'Mockery.php';

require_once APPLICATION_PATH . '/services/User.php';

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
        $this->object = new App_Service_User($this->em);
    }

    public function teardown()
    {
        m::close();
    }

    public function  testGetUsersList()
    {
        $result = array(
            new App_Model_Domain_User(),
            new App_Model_Domain_User()
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
        $result = new App_Model_Domain_User(array('name' => $testUserName));
        
        $this->em->shouldReceive('find')->once()->andReturn($result);
        $this->object = new App_Service_User($this->em);
        
        $user = $this->object->getUserById(1);
        $this->assertEquals($result, $user);
        
    }
    public function testRegisterNewUser()
    {
        $testUserName = 'TestUserName' . rand(1,100);
        $testUserPassword = md5($testUserName);
        $userData = array(
            'login' => $testUserName,
            'password' => $testUserPassword
        );

        $this->em->shouldReceive('persist');
        $this->em->shouldReceive('flush');

        $user = $this->object->registerNewUser($userData);

        $this->assertEquals($userData['login'], $user->login);
        $this->assertEquals($userData['password'], $user->password);
    }
}
