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

/**
 * Default validator for User Domain Object
 *
 * @author yugeon
 */

namespace App\Core\Model\Domain\Validator;
use \Xboom\Model\Validate\AbstractValidator,
    \Xboom\Model\Validate\Element\BaseValidator,
    \Xboom\Validate\UniqueField;

class UserValidator extends AbstractValidator
{
    public function init()
    {
        // name
        $nameValidator = new BaseValidator();
        $nameValidator->addValidator(new \Zend_Validate_NotEmpty(), true)
                      ->addValidator(new \Zend_Validate_StringLength(
                              array('min' => 1, 'max' => 50)))
                      ->addFilter(new \Zend_Filter_StringTrim());
        $this->addPropertyValidator('name', $nameValidator, true);

        // email
        $emailValidator = new BaseValidator();
        $emailValidator->addValidator(new \Zend_Validate_NotEmpty(), true)
                       ->addValidator(new \Zend_Validate_EmailAddress())
                       ->addFilter(new \Zend_Filter_StringTrim());
        $this->addPropertyValidator('email', $emailValidator, true);

        // password
        $passwordValidator = new BaseValidator();
        $passwordValidator->addValidator(new \Zend_Validate_NotEmpty(), true)
                          ->addValidator(new \Zend_Validate_StringLength(
                              array('min' => 4, 'max' => 32)))
                          ->addFilter(new \Zend_Filter_StringTrim())
                          ->setObscureValue(true);
        $this->addPropertyValidator('password', $passwordValidator, true);

    }
}
