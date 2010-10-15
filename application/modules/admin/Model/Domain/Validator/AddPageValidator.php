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
 * Description of AddPageValidator
 *
 * @author yugeon
 */

namespace App\Admin\Model\Domain\Validator;

use \Xboom\Model\Validate\AbstractValidator,
 \Xboom\Model\Validate\Element\BaseValidator;

class AddPageValidator extends AbstractValidator
{

    public function init()
    {
        // label
        $labelValidator = new BaseValidator();
        $labelValidator->addValidator(new \Zend_Validate_NotEmpty(), true)
                ->addValidator(new \Zend_Validate_StringLength(
                                array('min' => 1, 'max' => 50)))
                ->addFilter(new \Zend_Filter_StringTrim());
        $this->addPropertyValidator('label', $labelValidator, true);

        // title
        $titleValidator = new BaseValidator();
        $titleValidator->addValidator(new \Zend_Validate_StringLength(
                                array('max' => 255)))
                ->addFilter(new \Zend_Filter_StringTrim());
        $this->addPropertyValidator('title', $titleValidator);

        // css class
        $classValidator = new BaseValidator();
        $classValidator->addValidator(new \Zend_Validate_StringLength(
                                array('min' => 1, 'max' => 50)))
                ->addValidator(new \Zend_Validate_Regex('/^[a-zA-Z][-_a-zA-Z0-9]*$/'))
                ->addFilter(new \Zend_Filter_StringTrim());
        $this->addPropertyValidator('class', $classValidator);

        // target
        $targetValidator = new BaseValidator();
        $targetValidator->addValidator(new \Zend_Validate_InArray(array(
                            '_self', '_blank', '_parent', '_top'
                        )))
                ->addFilter(new \Zend_Filter_StringTrim());
        $this->addPropertyValidator('target', $targetValidator);

        // type
        $typeValidator = new BaseValidator();
        $typeValidator->addValidator(new \Zend_Validate_NotEmpty(), true)
                ->addValidator(new \Zend_Validate_InArray(array(
                            'mvc', 'uri'
                        )))
                ->addFilter(new \Zend_Filter_StringTrim());
        $this->addPropertyValidator('type', $typeValidator, true);

        // order
        $orderValidator = new BaseValidator();
        $orderValidator->addValidator(new \Zend_Validate_Int())
                ->addFilter(new \Zend_Filter_StringTrim())
                ->addFilter(new \Zend_Filter_Int());
        $this->addPropertyValidator('order', $orderValidator);

        // module
        $moduleValidator = new BaseValidator();
        $moduleValidator->addValidator(new \Zend_Validate_StringLength(
                                array('min' => 1, 'max' => 50)))
                ->addValidator(new \Zend_Validate_Regex('/^[a-zA-Z0-9]*$/'))
                ->addFilter(new \Zend_Filter_StringTrim());
        $this->addPropertyValidator('module', $moduleValidator);

        // controller
        $controllerValidator = new BaseValidator();
        $controllerValidator->addValidator(new \Zend_Validate_StringLength(
                                array('min' => 1, 'max' => 50)))
                ->addValidator(new \Zend_Validate_Regex('/^[a-zA-Z0-9]*$/'))
                ->addFilter(new \Zend_Filter_StringTrim());
        $this->addPropertyValidator('controller', $controllerValidator);

        // action
        $actionValidator = new BaseValidator();
        $actionValidator->addValidator(new \Zend_Validate_StringLength(
                                array('min' => 1, 'max' => 50)))
                ->addValidator(new \Zend_Validate_Regex('/^[a-zA-Z0-9]*$/'))
                ->addFilter(new \Zend_Filter_StringTrim());
        $this->addPropertyValidator('action', $actionValidator);

        // params
        $paramsValidator = new BaseValidator();
        $paramsValidator->addFilter(new \Zend_Filter_StringTrim());
        $this->addPropertyValidator('params', $paramsValidator);

        // route
        $routeValidator = new BaseValidator();
        $routeValidator->addValidator(new \Zend_Validate_StringLength(
                                array('min' => 1, 'max' => 50)))
                ->addValidator(new \Zend_Validate_Regex('/^[a-zA-Z0-9]*$/'))
                ->addFilter(new \Zend_Filter_StringTrim());
        $this->addPropertyValidator('route', $routeValidator);

        // resetParams
        $resetParamsValidator = new BaseValidator();
        $resetParamsValidator->addValidator(new \Zend_Validate_StringLength(
                                array('min' => 1, 'max' => 1)))
                ->addFilter(new \Zend_Filter_StringTrim())
                ->addFilter(new \Zend_Filter_StripTags());
        $this->addPropertyValidator('resetParams', $resetParamsValidator);

        // uri
        $uriValidator = new BaseValidator();
        $uriValidator->addValidator(new \Zend_Validate_StringLength(
                                array('min' => 1, 'max' => 255)))
                ->addFilter(new \Zend_Filter_StringTrim())
                ->addFilter(new \Zend_Filter_StripTags());
        $this->addPropertyValidator('uri', $uriValidator);

        // isActive
        $isActiveValidator = new BaseValidator();
        $isActiveValidator->addValidator(new \Zend_Validate_StringLength(
                                array('min' => 1, 'max' => 1)))
                ->addFilter(new \Zend_Filter_StringTrim())
                ->addFilter(new \Zend_Filter_StripTags());
        $this->addPropertyValidator('isActive', $isActiveValidator);

        // isVisible
        $isVisibleValidator = new BaseValidator();
        $isVisibleValidator->addValidator(new \Zend_Validate_StringLength(
                                array('min' => 1, 'max' => 1)))
                ->addFilter(new \Zend_Filter_StringTrim())
                ->addFilter(new \Zend_Filter_StripTags());
        $this->addPropertyValidator('isVisible', $isVisibleValidator);

//        // resource
//        $resourceValidator = new BaseValidator();
//        $resourceValidator->addValidator(new \Zend_Validate_StringLength(
//                                array('min' => 1, 'max' => 50)))
//                ->addFilter(new \Zend_Filter_StringTrim())
//                ->addFilter(new \Zend_Filter_StripTags());
//        $this->addPropertyValidator('resource', $resourceValidator);
        // privilege
        $privilegeValidator = new BaseValidator();
        $privilegeValidator->addValidator(new \Zend_Validate_StringLength(
                                array('min' => 1, 'max' => 50)))
                ->addFilter(new \Zend_Filter_StringTrim());
        $this->addPropertyValidator('privilege', $privilegeValidator);
    }

}
