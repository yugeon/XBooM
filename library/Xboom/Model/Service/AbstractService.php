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

namespace Xboom\Model\Service;
use \Xboom\Model\Form\Mediator;

/**
 * Description of Abstract
 *
 * @author yugeon
 */
abstract class AbstractService implements ServiceInterface
{

    /**
     * Used to inform what model class controlled by this service.
     * Required. Full qualified form.
     *
     * @var string
     */
    protected $_modelClassPrefix = '';
    /**
     * Used to inform what model name controlled by this service.
     * Required. Short name.
     *
     * @var string
     */
    protected $_modelName = '';
    /**
     * For automatic instantiation validators.
     *
     * @var string
     */
    protected $_validatorClassPrefix = '';
    /**
     * For automatic instantiation forms.
     *
     * @var string
     */
    protected $_formClassPrefix = '';
    /**
     * Contain model object which controlled by this service.
     *
     * @var \Xboom\Model\Domain\AbstractObject
     */
    protected $_model = null;
    /**
     * Contain mediators beetwen forms and model.
     *
     * @var array
     */
    protected $_mediators = array();
    /**
     * Contain forms.
     *
     * @var array
     */
    protected $_forms = array();
    /**
     * Contain validators.
     *
     * @var array
     */
    protected $_validators = array();


    /**
     * Set the model class name what controlled this service.
     *
     * @param string $modelClassPrefix
     * @return AbstractService
     */
    public function setModelClassPrefix($modelClassPrefix)
    {
        $this->_modelClassPrefix = $modelClassPrefix;
        return $this;
    }

    /**
     * Return the model class name what controlled this service.
     *
     * @return string
     * @throws \Xboom\Model\Service\Exception If model class is empty.
     */
    public function getModelClassPrefix()
    {
        if (empty($this->_modelClassPrefix))
        {
            throw new Exception('Model class can\'t be empty.');
        }
        return $this->_modelClassPrefix;
    }

    /**
     * Set the model name what controlled this service.
     *
     * @param string $modelName
     * @return AbstractService
     */
    public function setModelName($modelName)
    {
        $this->_modelName = $modelName;
        return $this;
    }

    /**
     * Return the model name what controlled this service.
     *
     * @return string
     * @throws \Xboom\Model\Service\Exception If model name is empty.
     */
    public function getModelName()
    {
        if (empty($this->_modelName))
        {
            throw new Exception('Model name can\'t be empty.');
        }
        return $this->_modelName;
    }

    /**
     * To inject model object.
     *
     * @param \Xboom\Model\Domain\AbstractObject $modelObject
     * @return AbstractService to provice fluent interface.
     */
    public function setModel($modelObject)
    {
        $this->_model = $modelObject;
        return $this;
    }

    /**
     * Retrieve model object.
     *
     * @return \Xboom\Model\Domain\AbstractObject
     * @throws \Xboom\Model\Service\Exception if can't create model object.
     */
    public function getModel()
    {
        if (null === $this->_model)
        {
            $modelName = $this->getModelClassPrefix() . '\\' . $this->getModelName();
            if (\class_exists($modelName))
            {
                $this->setModel(new $modelName);
            }
            else
            {
                throw new Exception("Class {$modelName} not exist");
            }
        }

        return $this->_model;
    }

    /**
     * Set validator class prefix for automaitc instantiation validator objects.
     *
     * @param string $prefix
     */
    public function setValidatorClassPrefix($prefix)
    {
        $this->_validatorClassPrefix = $prefix;
        return $this;
    }

    /**
     * Return current validator class prefix.
     *
     * @return string
     */
    public function getValidatorClassPrefix()
    {
        return $this->_validatorClassPrefix;
    }
    
    /**
     * Retrieve model object with validator $validatorName.
     *
     * @param empty|string $validatorName
     * @return \Xboom\Model\Domain\AbstractObject
     */
    public function getModelWithValidator($validatorName = '')
    {
        if (null === $this->_model)
        {
            $this->getModel();
        }

        if (empty($validatorName))
        {
            $validatorName = $this->getModelName();
        }

        $validator = $this->getValidator($validatorName);
        $this->_model->setValidator($validator);

        return $this->_model;
    }

    /**
     * Inject validator.
     *
     * @param string $validatorName
     * @param ValidatorInterface $validatorObject
     * @throws \InvalidArgumentExceptoin if $validatorObject is not validator.
     */
    public function setValidator($validatorName, $validatorObject)
    {
        if (!($validatorObject instanceof \Xboom\Model\Validate\ValidatorInterface))
        {
            throw new \InvalidArgumentException("Validator '{$validatorName}'"
                    . 'not instance of ValidatorInterface.');
        }

        $this->_validators[$validatorName] = $validatorObject;
    }

    /**
     * Retrieve validator. If $validatorName is exist then return specified validator.
     * If $validatorName not spesified then try create default validator for model.
     *
     * @param empty|string $validatorName
     * @return \Xboom\Model\Validate\ValidatorInterface
     * @throws \InvalidArgumentException If invalid $validatorName of can't create validator.
     */
    public function getValidator($validatorName = '')
    {
        if (empty($validatorName))
        {
            $validatorName = $this->getModelName();
        }

        if (isset($this->_validators[$validatorName]))
        {
            return $this->_validators[$validatorName];
        }

        $validatorClass = $this->getValidatorClassPrefix() . "\\{$validatorName}Validator";
        if (\class_exists($validatorClass))
        {
            $validatorObject = new $validatorClass;
            $this->setValidator($validatorName, $validatorObject);
            return $this->_validators[$validatorName];
        }
        throw new \InvalidArgumentException("Validator '{$validatorName}Validator' not found.");
    }

    /**
     * Set form class prefix for automaitc instantiation form objects.
     *
     * @param string $prefix
     */
    public function setFormClassPrefix($prefix)
    {
        $this->_formClassPrefix = $prefix;
        return $this;
    }

    /**
     * Return form class prefix.
     *
     * @return string
     */
    public function getFormClassPrefix()
    {
        return $this->_formClassPrefix;
    }

    /**
     * Inject specified form.
     *
     * @param string $formName
     * @param \Zend_Form $formObject
     * @throws \InvalidArgumentException
     */
    public function setForm($formName, $formObject)
    {
        if (!($formObject instanceof \Zend_Form))
        {
            throw new \InvalidArgumentException("Form '{$formName}' not instance of Zend_Form.");
        }

        $this->_forms[$formName] = $formObject;
    }

    /**
     * Retrieve specifed form or try create it.
     *
     * @param string $formName
     * @return \Zend_Form
     * @throws \InvalidArgumenException
     */
    public function getForm($formName)
    {
        if (isset($this->_forms[$formName]))
        {
            return $this->_forms[$formName];
        }

        $formClass = $this->getFormClassPrefix() . "\\{$formName}Form";
        if (\class_exists($formClass))
        {
            $formObject = new $formClass;
            $this->setForm($formName, $formObject);
            return $this->_forms[$formName];
        }
        throw new \InvalidArgumentException("Form '{$formName}' not found.");
    }

    /**
     * For inject mediator.
     *
     * @param string $mediatorName
     * @param \Xboom\Model\Form\MediatorInterface $mediator
     * @return AbstractService
     * @throws \InvalidArgumenException
     */
    public function setFormToModelMediator($mediatorName, $mediator)
    {
        if (!\is_string($mediatorName))
        {
            throw new \InvalidArgumentException('Mediator name must be a string');
        }

        if (!($mediator instanceof \Xboom\Model\Form\MediatorInterface))
        {
            throw new \InvalidArgumentException('Mediator object must be an instance of MediatorInterface');
        }

        $this->_mediators[$mediatorName] = $mediator;

        return $this;
    }

    /**
     * Return mediator by name if exist or try create it.
     *
     * @param string $mediatorName
     * @param null|Zend_Form $form
     * @param null|\Xboom\Model\Domain\AbstractObject $model
     * @return \Xboom\Model\Form\MediatorInterface
     */
    public function getFormToModelMediator($mediatorName, $form = null, $model = null)
    {
        if (isset($this->_mediators[$mediatorName]))
        {
            return $this->_mediators[$mediatorName];
        }

        if (null === $form)
        {
            $form = $this->getForm($mediatorName);
        }

        if (null === $model)
        {
            $model = $this->getModelWithValidator($mediatorName);
        }

        $mediator = new Mediator($form, $model);
        $this->setFormToModelMediator($mediatorName, $mediator);

        return $this->_mediators[$mediatorName];
    }

}
