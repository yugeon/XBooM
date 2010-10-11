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
use \Xboom\Model\Validate\ValidatorInterface,
    \Xboom\Model\Domain\ValidateInterface;

class Mediator implements MediatorInterface
{

    /**
     * Form
     *
     * @var \Zend_Form
     */
    protected $_form = null;

    /**
     * Model
     *
     * @var \Xboom\Model\Domain\AbstractObject
     */
    protected $_model = null;

    /**
     *
     * @var ValidatorInterface
     */
    protected $_domainValidator = null;

    protected $_values = array();
    /**
     * Create mediator, which connect form and model validator.
     *
     * @param string $form Name for form and validator.
     */
    public function __construct($formObject, $modelObject)
    {
        $this->setForm($formObject);
        $this->setModel($modelObject);
    }

    /**
     * For inject form.
     *
     * @param string $formObject
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
     * Return form for this mediator.
     *
     * @return object \Zend_Form
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * For inject model.
     *
     * @param DomainObject $modelObject
     * @return Mediator
     */
    public function setModel($modelObject)
    {
        if (!($modelObject instanceof ValidateInterface))
        {
            throw new \InvalidArgumentException('Argument must be a instance of '
                    . '\\Xboom\\Model\\Domain\AbstractObject');
        }

        $this->_model = $modelObject;

        return $this;
    }

    /**
     * Return model.
     *
     * @return \Xboom\Model\Domain\AbstractObject
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * Return current validator from model.
     *
     * @return \Xboom\Model\Validate\ValidatorInterface
     */
    public function getValidator()
    {
        if (null !== $this->_model)
        {
            return $this->_model->getValidator();
        }
    }

    /**
     *
     * @param ValidatorInterface $domainValidator
     * @return Mediator
     */
    public function setDomainValidator($domainValidator)
    {
        if (! ($domainValidator instanceof ValidatorInterface))
        {
            throw new \InvalidArgumentException(
                    'Domain validator must be instance of ValidatorInterface');
        }

        $this->_domainValidator = $domainValidator;
        return $this;
    }

    /**
     *
     * @return ValidatorInterface
     */
    public function getDomainValidator()
    {
        return $this->_domainValidator;
    }

    /**
     * Push data to model.
     *
     */
    public function _pushDataToModel()
    {
        // potential security violation,
        // becaouse sets all transfered properties
//        $values = $this->getValues();
//        $this->_model->setData($values);
    }

    /**
     * Fill form values filtered values
     *
     * @param <type> $form
     * @param <type> $validator
     */
    protected function _fillFormValues($form, $validator)
    {
        $values = $validator->getValues();
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
    protected function _fillFormErrors($validator)
    {
        $form = $this->getForm();
        if (null === $form || null === $validator)
        {
            return;
        }

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
     * Else check Domain Object use current validator.
     * Valid data push to model object.
     * Errors push to form.
     *
     * @param array $data
     * @param boolean $break1 Break validation if form is not valid.
     * @param boolean $break2 Break validation if model is not valid.
     * @return boolean true if $data is valid
     */
    public function isValid($data, $break1 = true, $break2 = true)
    {
        $isDataValid = false;

        $this->_setValues($data);

        $isDataValid = $this->_isFormValid();
        if ($isDataValid || !$break1)
        {
            $isDataValid = $this->_isModelValid() && $isDataValid;
            if ($isDataValid || !$break2)
            {
                $isDataValid = $this->_isDomainValid() && $isDataValid;
            }
        }

        if ($isDataValid)
        {
            $this->_pushDataToModel();
        }

        return $isDataValid;
    }

    protected function _isFormValid()
    {
        $form = $this->getForm();
        $isFormValid = $form->isValid($this->getValues());
        $this->_setValues($form->getValues());
        return (boolean)$isFormValid;
    }

    protected function _isModelValid()
    {
        $isModelValid = true;
        $validator = $this->getValidator();
        if (null !== $validator)
        {
            $isModelValid = $validator->isValid($this->getValues());
            if (!$isModelValid)
            {
                $this->_fillFormErrors($validator);
            }

            $this->_setValues($validator->getValues());
        }
        return (boolean)$isModelValid;
    }

    protected function _isDomainValid()
    {
        $isDomainValid = true;
        $domainValidator = $this->getDomainValidator();
        if (null !== $domainValidator)
        {
            $isDomainValid = $domainValidator->isValid($this->getValues());
            if (!$isDomainValid)
            {
                $this->_fillFormErrors($domainValidator);
            }

            $this->_setValues($domainValidator->getValues());
        }
        return (boolean)$isDomainValid;
    }

    /**
     * Return filtered values from validation.
     *
     * @return array
     */
    public function getValues()
    {
        return $this->_values;
    }

        /**
     * Merge $data with current values.
     *
     * @param array $data
     * @return Mediator
     */
    protected function _setValues($data)
    {
        $this->_values = \array_merge($this->_values, (array)$data);
        return $this;
    }
}
