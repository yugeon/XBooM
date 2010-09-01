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
 * Description of AbstractValidator
 *
 * @author yugeon
 */

namespace Xboom\Model\Validate\Element;

abstract class AbstractValidator implements ValidatorInterface
{

    /**
     * Validator chain
     *
     * @var array
     */
    protected $_validators = array();

    /**
     * Array of validation failure messages
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * Element filters
     * @var array
     */
    protected $_filters = array();

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
    public function addValidator($validator, $breakChainOnFailure = false)
    {
        if ($validator instanceof \Zend_Validate_Interface)
        {
            $this->_validators[] = array(
                'instance' => $validator,
                'breakChainOnFailure' => (boolean) $breakChainOnFailure
            );
        }
        else
        {
            throw new \InvalidArgumentException;
        }
        return $this;
    }

    /**
     * Retrieve all validators
     *
     * @return array
     */
    public function getValidators()
    {
        $validators = array();
        foreach ($this->_validators as $validator)
        {
            $validators[] = $validator['instance'];
        }
        return $validators;
    }

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  mixed $value Array not supported!
     * @return boolean
     * @throws \Zend_Valid_Exception If validation of $value is impossible
     * @throws \InvalidArgumentException If try validate Array value
     */
    public function isValid($value)
    {
        if (\is_array($value))
        {
            throw new \InvalidArgumentException('Validation of Array not support');
        }
        
        $isValid = true;
        $this->_messages = array();

        $value = $this->filter($value);

        foreach ($this->_validators as $element)
        {
            $validator = $element['instance'];
            if ($validator->isValid($value)) {
                continue;
            }
            $isValid = false;
            $messages = $validator->getMessages();
            $this->_messages = array_merge($this->_messages, $messages);
            if ($element['breakChainOnFailure']) {
                break;
            }
        }
        
        return $isValid;
    }

    /**
     * Returns an array of messages that explain why the most recent isValid()
     * call returned false. The array keys are validation failure message identifiers,
     * and the array values are the corresponding human-readable message strings.
     *
     * If isValid() was never called or if the most recent isValid() call
     * returned true, then this method returns an empty array.
     *
     * @return array
     */
    public function getMessages()
    {
        return array_unique($this->_messages);
    }

    /**
     * Add a filter to the element
     *
     * @param  \Zend_Filter_Interface $filter
     * @return ValidatorInterface Provides a fluent interface
     * @throws \InvalidArgumentException If $filter is not instance of \Zend_Filter_Interface
     */
    public function addFilter($filter)
    {
        if ($filter instanceof \Zend_Filter_Interface)
        {
            $filterName = \get_class($filter);
            $this->_filters[$filterName] = $filter;
        }
        else
        {
            throw new \InvalidArgumentException;
        }
        return $this;
    }
    /**
     * Get all filters
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->_filters;
    }

    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     * @throws Zend_Filter_Exception If filtering $value is impossible
     * @return mixed
     */
    public function filter($value)
    {
        foreach ($this->getFilters() as $filter)
        {
            $value = $filter->filter($value);
        }
        return $value;
    }

    /**
     * Set flag indicating whether or not value should be obfuscated in messages
     *
     * @param  bool $flag
     * @return Zend_Validate_Abstract
     */
    public function setObscureValue($flag)
    {
        foreach ($this->getValidators() as $validator)
        {
            $validator->setObscureValue($flag);
        }
    }

}
