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
 * Description of ConditionalRendering
 *
 * @author yugeon
 */
namespace Xboom\Form\Decorator\JQuery;

class ConditionalRendering extends \Zend_Form_Decorator_Abstract
{
    protected $_view = null;

    /**
     * Decorate content and/or element
     *
     * @param  string $content
     * @return string
     * @throws Zend_Form_Decorator_Exception when unimplemented
     */
    public function render($content)
    {
        $element = $this->getElement();
        $this->view = $element->getView();
        if (null === $this->view) {
            return $content;
        }

        $options = $this->getOptions();

        $actuatorId = isset($options['actuatorId'])? $options['actuatorId'] : null;
        $actuatorValue = isset($options['actuatorValue'])? $options['actuatorValue'] : null;

        if (empty($actuatorId) || empty($actuatorValue))
            return $content;

        $id = (string) $element->getId();

        if (\array_key_exists('id', $options))
        {
            if (!empty($options['id']))
            {
                $id = (string) $options['id'];
            }
        }

        $id = 'condRend-' . $id;
        $outputId = ' id="' . $id . '"';
        
        $this->_enableJQuery();
        $code = $this->_getConditionalRenderingCode($actuatorId, $actuatorValue, $id);
        $this->view->JQuery()->addOnLoad($code);

        return "<div{$outputId}>" . $content . '</div>';
    }

    /**
     * Add helper paths for jQuery view helpers if it hav not done.
     *
     * @return ConditionalRendering
     */
    protected function _enableJQuery()
    {
        if (false === $this->view->getPluginLoader('helper')->getPaths('ZendX_JQuery_View_Helper')) {
            $this->view->addHelperPath('ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper');
        }
        return $this;
    }

    /**
     * Retrieve jQuery code that controls the conditional rendering.
     *
     * @param string $actuatorId The ID of the element that control depending element
     * @param string $actuatorValue Value at which the dependent element will be shown
     * @param string $dependentId The ID of the element that will hide or show
     * @return string jQuery code
     */
    protected function _getConditionalRenderingCode($actuatorId, $actuatorValue, $dependentId)
    {
        $code = <<<jQueryCode

    if ($("#$actuatorId").val() != "$actuatorValue")
        $("#$dependentId").hide();
    $("#$actuatorId").change(function(){
        if ($(this).val() == "$actuatorValue")
            $("#$dependentId").show("slow");
        else $("#$dependentId").hide();
    });
jQueryCode;
        
        return $code;
    }
}
