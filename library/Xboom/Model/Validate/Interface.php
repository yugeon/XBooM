<?php

/**
 *
 * @author yugeon
 */
interface Xboom_Model_Validate_Interface extends Zend_Validate_Interface
{

     /**
     * Add a new element
     *
     * $element may be either a string element type, or an object of type
     * Zend_Form_Element. If a string element type is provided, $name must be
     * provided, and $options may be optionally provided for configuring the
     * element.
     *
     * If a Xboom_Model_Element_Interface is provided, $name may be optionally provided,
     * and any provided $options will be ignored.
     *
     * @param  string|Xboom_Model_Element_Interface $element
     * @param  string $name
     * @param  array $options
     * @return Xboom_Model_Validate_Interface Provides a fluent interface
     */
    public function addElement($element, $name = null, $options = null);

}