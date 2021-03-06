<?php
/**
 *  CMF for web applications based on Zend Framework 1 and Doctrine 2
 *  Copyright (C) 2010  Eugene Gruzdev aka yugeon
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright  Copyright (c) 2010 yugeon <yugeon.ru@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html  GNU GPLv3
 */

namespace Core\Model\Form;
/**
 * Description of RegisterNewUserForm
 *
 * @author yugeon
 */
class RegisterUserForm extends \Zend_Form
{

    public function init()
    {
        $this->setName('registerUser');

        $this->addElement('text', 'name', array(
            'label' => 'Username',
            //'required' => true
        ));

        $this->addElement('text', 'email', array(
            'label' => 'Email address',
//            'required' => true
        ));

        $this->addElement('password', 'password', array(
            'label' => 'Password',
//            'required' => true
        ));

        $this->addElement('password', 'confirm_password', array(
            'label' => 'Confirm password',
            'required' => true,
            'validators' => array( array('Identical', false, 'password'))
        ));

        $this->addElement('captcha', 'captcha', array(
            'label' => 'Captcha',
            'captcha' => array(
                'captcha' => 'Image',
                'wordLen' => 6,
                'timeout' => 300,
                'font' => \APPLICATION_PATH . '/configs/fonts/Glasten_Bold.ttf',
            ),
        ));

        $this->addElement('submit', 'register', array(
            'label' => 'Register'
        ));
    }

}
