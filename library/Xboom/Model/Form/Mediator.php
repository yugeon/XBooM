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
 * Description of Mediator
 *
 * @author yugeon
 */
namespace Xboom\Model\Form;
use \Xboom\Model\Validate\ValidatorInterface;

class Mediator implements MediatorInterface
{

    /**
     * Mediator name.
     *
     * @var string
     */
    protected $_name;

    /**
     * Form for this mediator with name $_name
     *
     * @var \Zend_Form
     */
    protected $_form = null;

    /**
     * Validator with name $_name
     *
     * @var \Xboom\Model\Validate\ValidatorInterface
     */
    protected $_validator = null;

    /**
     * Create mediator, which connect form and model validator.
     *
     * @param string $name Name for form and validator.
     */
    public function __construct($name)
    {
        $this->setName($name);
    }

    /**
     * Sets the name, in which the searched form and validator
     *
     * @param string $name
     * @return Mediator
     * @throws \InvalidArgumentException If $name not string.
     */
    public function setName($name)
    {
        if (!\is_string($name))
        {
            throw new \InvalidArgumentException('Argument must be a string');
        }
        
        $this->_name = $name;

        return $this;
    }

    /**
     * Return form for this mediator.
     *
     * @return object \Zend_Form
     * @throws \InvalidArgumentException If form not exists.
     */
    public function getForm()
    {
        if (null !== $this->_form)
        {
            return $this->_form;
        }

        // FIXME hardcode namespace
        $formClass = "\\Core\\Model\\Form\\{$this->_name}Form";
        if (\class_exists($formClass))
        {
            $formObject = new $formClass;
            $this->setForm($formObject);
            return $this->_form;
        }
        throw new \InvalidArgumentException("Form '{$this->_name}' not found.");
    }

    /**
     * For inject form.
     *
     * @param string $formName
     * @param \Zend_Form $formObject
     * @return Mediator
     */
    public function setForm($formObject)
    {

        if (!($formObject instanceof \Zend_Form))
        {
            throw new \InvalidArgumentException('Form object must be instance of Zend_Form');
        }

        $this->_form = $formObject;

        return $this;
    }

    /**
     * Return validator for User Object by validator name.
     *
     * @return \Xboom\Model\Validate\ValidatorInterface
     */
    public function getValidator()
    {
        if (null !== $this->_validator)
        {
            return $this->_validator;
        }

        // FIXME hardcode namespace
        $validatorClass = "\\Core\\Model\\Domain\\Validator\\{$this->_name}Validator";
        if (\class_exists($validatorClass))
        {
            $validatorObject = new $validatorClass;
            $this->setValidator($validatorObject);
            return $this->_validator;
        }
        throw new \InvalidArgumentException("Validator '{$this->_name}' not found.");
    }

    /**
     * For inject validator.
     *
     * @param \Xboom\Model\Validate\ValidatorInterface $validatorObject
     * @return UserService
     */
    public function setValidator($validatorObject)
    {
        if (!($validatorObject instanceof ValidatorInterface))
        {
            throw new \InvalidArgumentException('Form object must be instance of ValidatorInterface');
        }

        $this->_validator = $validatorObject;

        return $this;
    }

    /**
     * Fill form values filterd values
     *
     * @param <type> $form
     * @param <type> $validator
     */
    protected function _fillFormValues($form, $validator)
    {
        $values = $this->getValues();
        if (empty($values))
        {
            return;
        }
        
        $formElements = $form->getElements();
        foreach ($formElements as $key => $element)
        {
            if (isset($values[$key]))
            {
                $element->setValue($values[$key]);
            }
        }
    }

    /**
     * Fill form data validation errors.
     *
     * @param <type> $form
     * @param <type> $validator
     */
    protected function _fillFormErrors($form, $validator)
    {
        $messages = $validator->getMessages();
        $formElements = $form->getElements();
        foreach ($formElements as $key => $element)
        {
            if (isset($messages[$key]))
            {
                $element->addErrors($messages[$key]);
            }
        }
    }

    /**
     * First check is form valid with this $data.
     * If form is not valid and $break == true, then return false.
     * Else check Domain Object validator.
     *
     * @param array $data
     * @param boolean $break Break validation if form is not valid.
     * @return boolean true if $data is valid
     */
    public function isValid($data, $break = true)
    {
        $form = $this->getForm();
        $isFormValid = $form->isValid($data);
        if (!$isFormValid && $break)
        {
            return false;
        }

        $validator = $this->getValidator();
        $isDataValid = $validator->isValid($data);

        $this->_fillFormValues($form, $validator);

        if ($isFormValid && $isDataValid)
        {
            return true;
        }

        // Fill form data validation errors.
        if (!$isDataValid)
        {
            $this->_fillFormErrors($form, $validator);
        }

        return false;
    }

    /**
     * Return filtered values from validation.
     *
     * @return array
     */
    public function getValues()
    {
        $result = array();

        $validator =$this->getValidator();
        foreach ($validator->getPropertiesForValidation() as $property => $element)
        {
            $result[$property] = $element->getValue();
        }

        return $result;
    }
}
