<?php

namespace test\App\Core\Model\Form;
use \App\Core\Model\Form\RegisterUserForm;

class RegisterUserFormTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var RegisterUserForm
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new RegisterUserForm;
    }


    public function testInit()
    {
        $this->assertArrayHasKey('name', $this->object->getElements());
        $this->assertArrayHasKey('email', $this->object->getElements());
        $this->assertArrayHasKey('password', $this->object->getElements());
        $this->assertArrayHasKey('confirm_password', $this->object->getElements());
        $this->assertArrayHasKey('captcha', $this->object->getElements());
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
            'name' => 'ddd',
            'email' => 'ddd',
            'password' => $password,
            'confirm_password' => $confirmPassword,
        );

        $this->object->removeElement('captcha');

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

        $this->object->removeElement('captcha');

        $this->assertFalse($this->object->isValid($data));
    }
}