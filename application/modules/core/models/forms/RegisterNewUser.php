<?php
namespace Core\Model\Form;
/**
 * Description of RegisterNewUserForm
 *
 * @author yugeon
 */
class RegisterNewUser extends \Zend_Form
{

    public function init()
    {
        $username = new \Zend_Form_Element_Text('username');

        $password = new \Zend_Form_Element_Password('password');

        $submit = new \Zend_Form_Element_Submit('register');

        $this->addElements(array(
            $username,
            $password,
            $submit
        ));
    }

}
