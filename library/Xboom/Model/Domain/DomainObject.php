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
 * Description of DomainObject
 *
 * @author yugeon
 */
namespace Xboom\Model\Domain;
use \Xboom\Model\Validate\Exception as ValidateException;

class DomainObject extends AbstractObject implements ObjectInterface, ValidateInterface
{
    /**
     * Contain short name this object.
     *
     * @var string
     */
    protected $_shortName = '';
    /**
     * Current validator for this domain object.
     *
     * @var Xboom\Model\Validate\ValidatorInterface
     */
    protected $_validator;

    /**
     * Marker is dirty this domain object or not.
     *
     * @var boolean
     */
    protected $_isDirty = true;
    
    /**
     * Default constructor.
     * If $data exist, then assign to properties by key.
     *
     * @param array $data
     */
    public function __construct(array $data = null)
    {
        parent::__construct($data);

        $this->_markDirty();
    }

    /**
     * Map a call to set a property to its corresponding mutator if it exists.
     * Otherwise, set the property directly.
     *
     * Ignore any properties that begin with an underscore so not all of our
     * protected properties are exposed.
     *
     * @param  string $name Name of property
     * @param  mixed  $value Value of property
     * @return mixed Default return this object
     * @throws \InvalidArgumentException If no mutator/property exists by that name
     */
    public function __set($name, $value)
    {
        parent::__set($name, $value);
        $this->_markDirty();
    }
    
    // ------------------------------
    //  Implements ObjectInterface
    // ------------------------------

    /**
     * Return short name this class without the namespace/
     *
     * @return string
     */
    public function  _getObjectName()
    {
        if (empty($this->_shortName))
        {
            $thisObject = new \ReflectionObject($this);
            $this->_shortName = $thisObject->getShortName();
        }

        return $this->_shortName;
    }

    /**
     * Mark this object as dirty.
     *
     * @param boolean $marker
     */
    protected function _markDirty($marker = true)
    {
        $this->_isDirty = $marker;
    }

    /**
     * Return true if this object clean.
     *
     * @return boolean
     */
    public function isDirty()
    {
        return $this->_isDirty;
    }

    // ------------------------------
    //  Implements ValidateInterface
    // ------------------------------

    /**
     * To inject values in object.
     *
     * @param array $data
     * @return AbstractObject
     */
    public function setData(array $data)
    {
        foreach ($data as $property => $value)
        {
            $accessor = 'set' . ucfirst($property);
            $this->{$accessor}($value);
        }

        return $this;
    }

    /**
     * Set validate object
     *
     * @param $validator Xboom\Model\Validate\ValidatorInterface
     * @return ValidateInterface for fluent.
     */
    public function setValidator($validator)
    {
        $this->_validator = $validator;
        return $this;
    }

    /**
     * Get validate object
     *
     * @return Xboom\Model\Validate\ValidatorInterface
     */
    public function getValidator()
    {

        return $this->_validator;
    }

    /**
     * Validate current Domain Object.
     *
     * If fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @return boolean
     * @throws \Xboom\Model\Validate\Exception If validation of $data is impossible
     */
    public function isValid()
    {
        if (null === $this->_validator)
        {
            throw new ValidateException('Validator is null');
        }

        $data = $this->toArray();
        $result = $this->_validator->isValid($data);

        // Mark this object as clear or dirty depending on validation result
        $this->_markDirty(!$result);

        return $result;
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
        if (null === $this->_validator)
        {
            return array();
        }
        else
        {
            return $this->_validator->getMessages();
        }
    }
}
