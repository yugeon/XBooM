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
 *
 * @author yugeon
 */
namespace Xboom\Model\Service;

interface ServiceInterface
{
    /**
     * Return the model class name what controlled this service.
     *
     * @return string
     * @throws \Xboom\Model\Service\Exception If model class is empty.
     */
    public function getModelClassPrefix();

    /**
     * Return the model name what controlled this service.
     *
     * @return string
     * @throws \Xboom\Model\Service\Exception If model name is empty.
     */
    public function getModelName();

    /**
     * To inject model object.
     *
     * @param \Xboom\Model\Domain\AbstractObject $modelObject
     * @return AbstractService to provice fluent interface.
     */
    public function setModel($modelObject);

    /**
     * Retrieve model object.
     *
     * @return \Xboom\Model\Domain\AbstractObject
     * @throws \Xboom\Model\Service\Exception if can't create model object.
     */
    public function getModel();

    /**
     * Retrieve model object with validator $validatorName.
     *
     * @param empty|string $validatorName
     * @return \Xboom\Model\Domain\AbstractObject
     */
    public function getModelWithValidator($validatorName = '');

    /**
     * Set validator class prefix for automaitc instantiation validator objects.
     *
     * @param string $prefix
     */
    public function setValidatorClassPrefix($prefix);
    
    /**
     * Return current validator class prefix.
     *
     * @return string
     */
    public function getValidatorClassPrefix();

    /**
     * Inject validator.
     *
     * @param string $validatorName
     * @param ValidatorInterface $validatorObject
     * @throws \InvalidArgumentExceptoin if $validatorObject is not validator.
     */
    public function setValidator($validatorName, $validatorObject);

    /**
     * Retrieve validator. If $validatorName is exist then return specified validator.
     * If $validatorName not spesified then try create default validator for model.
     *
     * @param empty|string $validatorName
     * @return \Xboom\Model\Validate\ValidatorInterface
     * @throws \InvalidArgumentException If invalid $validatorName of can't create validator.
     */
    public function getValidator($validatorName = '');

    /**
     * Set form class prefix for automaitc instantiation form objects.
     *
     * @param string $prefix
     */
    public function setFormClassPrefix($prefix);

    /**
     * Return form class prefix.
     *
     * @return string
     */
    public function getFormClassPrefix();

    /**
     * Inject specified form.
     *
     * @param string $formName
     * @param \Zend_Form $formObject
     * @throws \InvalidArgumentException
     */
    public function setForm($formName, $formObject);

    /**
     * Retrieve specifed form or try create it.
     *
     * @param string $formName
     * @return \Zend_Form
     * @throws \InvalidArgumenException
     */
    public function getForm($formName);

    /**
     * For inject mediator.
     *
     * @param string $mediatorName
     * @param \Xboom\Model\Form\MediatorInterface $mediator
     * @return AbstractService
     * @throws \InvalidArgumenException
     */
    public function setFormToModelMediator($mediatorName, $mediator);

    /**
     * Return mediator by name if exist or try create it.
     *
     * @param string $mediatorName
     * @param null|Zend_Form $form
     * @param null|\Xboom\Model\Domain\AbstractObject $model
     * @return \Xboom\Model\Form\MediatorInterface
     */
    public function getFormToModelMediator($mediatorName, $form = null, $model = null);
}
