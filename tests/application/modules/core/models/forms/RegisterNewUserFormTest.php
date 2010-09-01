<?php

use Core\Model\Form\RegisterNewUserForm;
require_once 'PHPUnit/Framework.php';

class RegisterNewUserFormTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var RegisterNewUserForm
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new RegisterNewUserForm;
    }


    public function testInit()
    {
        $this->assertArrayHasKey('name', $this->object->getElements());
        $this->assertArrayHasKey('login', $this->object->getElements());
        $this->assertArrayHasKey('password', $this->object->getElements());
        $this->assertArrayHasKey('confirm_password', $this->object->getElements());
        $this->assertArrayHasKey('register', $this->object->getElements());
    }
    public function  testMustBePost()
    {
        $this->assertEquals('post', $this->object->getMethod());
    }
    public function testFormMustBeValidIfPasswordAndConfirmPasswordEquals()
    {
        $password = '123456';
        $confirmPassword = '123456';

        $data = array(
            'password' => $password,
            'confirm_password' => $confirmPassword,
        );

        $this->assertTrue($this->object->isValid($data));
    }
    public function  testFormMustBeInvalidIfPasswordAndConfirmPasswordNotEquals()
    {
        $password = '123456';
        $confirmPassword = '654321';

        $data = array(
            'password' => $password,
            'confirm_password' => $confirmPassword,
        );

        $this->assertFalse($this->object->isValid($data));
    }
}