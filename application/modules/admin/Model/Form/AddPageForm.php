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
 * Description of AddMenuForm
 *
 * @author yugeon
 */

namespace App\Admin\Model\Form;
use \Xboom\Form\Decorator\JQuery\ConditionalRendering;

class AddPageForm extends \Zend_Form
{
    public function init()
    {
        $this->setName('addPage');

        $this->addElement('hidden', 'menuName');

        $this->addElement('text', 'label', array(
            'label' => 'Label',
        ));

        $this->addElement('text', 'title', array(
            'label' => 'Title',
        ));

        $this->addElement('text', 'class', array(
            'label' => 'CSS class',
        ));

        $this->addElement('Select', 'target', array(
            'label' => 'Target',
            'multiOptions' => array(
                '_self' => '_self',
                '_blank' => '_blank',
                '_parent' => '_parent',
                '_top' => '_top'
            ),
        ));

        $this->addElement('text', 'order', array(
            'label' => 'Order',
        ));

        $this->addElement('Select', 'type', array(
            'label' => 'Type',
            'multiOptions' => array('uri' => 'uri', 'mvc' => 'mvc'),
        ));

        $this->addElement('text', 'module', array(
            'label' => 'Module',
        ));

        $this->addElement('text', 'controller', array(
            'label' => 'Controller',
        ));

        $this->addElement('text', 'action', array(
            'label' => 'Action',
        ));

        $this->addElement('text', 'params', array(
            'label' => 'Params',
        ));

        $this->addElement('text', 'route', array(
            'label' => 'Route',
        ));

        $this->addElement('checkbox', 'resetParams', array(
            'label' => 'Reset params',
            'checked' => true,
        ));

        $this->addDisplayGroup(
                array('module', 'controller', 'action', 'params', 'route', 'resetParams'),
                'mvc-group'
        );

        $this->addElement('text', 'uri', array(
            'label' => 'URI',
        ));

        $this->addDisplayGroup(
                array('uri'),
                'uri-group'
        );

        // dynamic conditional rendering form elements
        $group = $this->getDisplayGroup('mvc-group');
        $group->addDecorator(new ConditionalRendering(array(
            'actuatorId' => 'type',
            'actuatorValue' => 'mvc',
            'id' => 'mvc-group'
        )));

        $group = $this->getDisplayGroup('uri-group');
        $group->addDecorator(new ConditionalRendering(array(
            'actuatorId' => 'type',
            'actuatorValue' => 'uri',
            'id' => 'uri-group'
        )));

        $this->addElement('checkbox', 'isActive', array(
            'label' => 'Is active',
            'checked' => false,
        ));
        
        $this->addElement('checkbox', 'isVisible', array(
            'label' => 'Is visible',
            'checked' => true,
        ));
        $this->addElement('text', 'resource', array(
            'label' => 'Resource',
        ));

        $this->addElement('text', 'privilege', array(
            'label' => 'Privilege',
        ));

        $this->addElement('submit', 'submit', array(
            'label' => 'Add',
            'required' => true,
        ));

        $this->addElement('reset', 'reset', array(
            'label' => 'Reset',
        ));
    }
}
