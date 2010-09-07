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

namespace Xboom\Model\Validate;

use Xboom\Model\Validate\Element\ValidatorInterface as ElementValidator;

abstract class AbstractValidator implements ValidatorInterface
{
    
    /**
     * Protected property.
     * Array of properties which affected to validation.
     * Where each key is name of property
     * and value is object implemented ElementValidator
     *
     * @var array
     */
    protected $_propertiesForValidation = array();

    protected $_messages = array();

    /**
     *
     * @var \Doctrine\ORM\EntitiyManager
     */
    protected $_em;

    /**
     *
     * @var string
     */
    protected $_entityClass;

    public function  __construct($em = null, $entityClass = '')
    {
        if (null !== $em)
        {
            $this->_em = $em;
        }

        if (!empty($entityClass))
        {
            $this->_entityClass = $entityClass;
        }

        $this->init();
    }

    /**
     * Initialize this object custom elements.
     *
     * @return void
     */
    public function init()
    {

    }

    public function getEntityManager()
    {
        return $this->_em;
    }

    public function getEntityClass()
    {
        return $this->_entityClass;
    }

    /**
     * Add validator for property.
     * $element must be an object of type ElementValidator.
     *
     * @param  string $propertyName
     * @param  ElementValidator $validator
     * @return ValidatorInterface Provides a fluent interface
     */
    public function addPropertyValidator($propertyName, ElementValidator $validator)
    {
        $this->_propertiesForValidation[$propertyName] = $validator;
        return $this;
    }

    /**
     * Return validator for property $propertyName.
     *
     * @param string $propertyName
     * @return object ElementValidator
     * @throws Xboom\Model\Validate\NoSuchPropertyException
     */
    public function getPropertyValidator($propertyName)
    {
        if (isset($this->_propertiesForValidation[$propertyName]))
        {
            return $this->_propertiesForValidation[$propertyName];
        }
        throw new NoSuchPropertyException("Validator for property '$propertyName' dos'n exist");
    }

    /**
     * Get Array of properties affected to validation
     *
     * @return array Array of ElementValidator
     */
    public function getPropertiesForValidation()
    {
        return $this->_propertiesForValidation;
    }

    /**
     * Validate data with associated validators by key.
     *
     * If $data not present, then use values from current Domain Object.
     * Otherwise, use the data, assigned by key with properties name.
     * If $data fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  array $data
     * @return boolean
     * @throws \InvalidArgumentException if $data is not array
     */
    public function isValid($data)
    {
        if (!\is_array($data))
        {
            throw new \InvalidArgumentException('$data must be array');
        }
        
        $isValid = true;

        $properties = $this->getPropertiesForValidation();
        foreach ($properties as $key => $propertyValidator)
        {
            if (\array_key_exists($key, $data))
            {
                $isValid = $propertyValidator->isValid($data[$key]) && $isValid;
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
        $this->_messages = array();
        $properties = $this->getPropertiesForValidation();
        foreach ($properties as $key => $propertyValidator)
        {
            $messages = $propertyValidator->getMessages();
            if (!empty($messages))
                $this->_messages[$key] = $messages;
        }
        return $this->_messages;
    }
}
