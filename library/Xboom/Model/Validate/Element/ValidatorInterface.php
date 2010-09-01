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

namespace Xboom\Model\Validate\Element;
/**
 *
 * @author yugeon
 */
interface ValidatorInterface extends \Zend_Validate_Interface, \Zend_Filter_Interface
{

    /**
     * Adds a validator to the end of the chain
     *
     * If $breakChainOnFailure is true, then if the validator fails, the next validator in the chain,
     * if one exists, will not be executed.
     *
     * @param  \Zend_Validate_Interface $validator
     * @param  boolean                  $breakChainOnFailure
     * @return ValidatorInterface       Provides a fluent interface
     * @throws \InvalidArgumentException If $validator is not instance of \Zend_Validate_Interface
     */
    public function addValidator($validator, $breakChainOnFailure = false);

    /**
     * Retrieve all validators
     *
     * @return array
     */
    public function getValidators();

    /**
     * Add a filter to the element
     *
     * @param  \Zend_Filter_Interface $filter
     * @return ValidatorInterface Provides a fluent interface
     * @throws \InvalidArgumentException If $filter is not instance of \Zend_Filter_Interface
     */
    public function addFilter($filter);

    /**
     * Get all filters
     *
     * @return array
     */
    public function getFilters();

    /**
     * Set flag indicating whether or not value should be obfuscated in messages
     *
     * @param  bool $flag
     * @return ValidatorInterface
     */
    public function setObscureValue($flag);

}