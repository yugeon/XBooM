<?php

require_once 'PHPUnit/Framework.php';

require_once APPLICATION_PATH . '/models/Domain/User.php';
class App_Model_Domain_UserTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Application_Model_Domain_User
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new App_Model_Domain_User;
    }

    public function testGetSetUserName()
    {
        $testName = 'Vasya';
        $this->object->setName($testName);
        $this->assertEquals($testName, $this->object->getName());
    }
    public function testInitUserFromConstruct()
    {
        $testUserName = 'TestUserName' . rand(1,100);
        $testUserPassword = md5($testUserName);
        $userData = array(
            'name' => $testUserName,
            'login' => $testUserName,
            'password' => $testUserPassword
        );
        $this->object = new App_Model_Domain_User($userData);
        $expected = $userData;
        $expected['id'] = null;
        $this->assertEquals($expected, $this->object->toArray());
    }
}