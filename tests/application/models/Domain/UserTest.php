<?php

require_once 'PHPUnit/Framework.php';

require_once APPLICATION_PATH . '/models/Domain/User.php';
class Application_Model_Domain_UserTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Application_Model_Domain_User
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new Application_Model_Domain_User;
    }

    public function testGetSetUserName()
    {
        $testName = 'Vasya';
        $this->object->setName($testName);
        $this->assertEquals($testName, $this->object->getName());
    }
}