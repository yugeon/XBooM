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
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}