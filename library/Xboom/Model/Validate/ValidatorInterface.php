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

namespace Xboom\Model\Validate;
use Xboom\Model\Validate\Element\ValidatorInterface as ElementValidator;
/**
 *
 * @author yugeon
 */
interface ValidatorInterface extends \Zend_Validate_Interface
{

    /**
     * Initialize this object with custom elements.
     *
     * @return void
     */
    public function init();
    
    /**
     * Add validator for property.
     * $element must be an object of type ElementValidator.
     *
     * @param  string $propertyName
     * @param  ElementValidator $validator
     * @return ValidatorInterface Provides a fluent interface
     */
    public function addPropertyValidator($propertyName, ElementValidator $validator);

    /**
     * Return validator for property $propertyName.
     *
     * @param string $propertyName
     * @return object ElementValidator
     * @throws Xboom\Model\Validate\NoSuchPropertyException
     */
    public function getPropertyValidator($propertyName);

    /**
     * Get Array of properties affected to validation
     *
     * @return array Array of ElementValidator
     */
    public function getPropertiesForValidation();

    /**
     * Get all filtered values.
     *
     * @return array
     */
    public function getValues();

}