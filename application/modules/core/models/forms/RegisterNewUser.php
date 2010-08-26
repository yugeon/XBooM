<?php

/**
 * Description of RegisterNewUserForm
 *
 * @author yugeon
 */
class Core_Model_Form_RegisterNewUser extends Zend_Form
{

    public function init()
    {
        $username = new Zend_Form_Element_Text('username');

        $password = new Zend_Form_Element_Password('password');

        $submit = new Zend_Form_Element_Submit('register');

        $this->addElements(array(
            $username,
            $password,
            $submit
        ));
    }

}
