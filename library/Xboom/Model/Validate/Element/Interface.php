<?php

/**
 *
 * @author yugeon
 */
interface Xboom_Model_Validate_Element_Interface
    extends Zend_Validate_Interface, Zend_Filter_Interface
{
    /**
     * Return validator chain
     *
     * @return array Array of Xboom_Model_Validate_Interface elements
     */
    public function getValidators();

    /**
     * Adds a validator to the end of the chain
     *
     * If $breakChainOnFailure is true, then if the validator fails, the next validator in the chain,
     * if one exists, will not be executed.
     *
     * @param  Xboom_Model_Validate_Interface $validator
     * @param  boolean                        $breakChainOnFailure
     * @return Xboom_Model_Validate_Interface Provides a fluent interface
     */
    public function addValidator(Xboom_Model_Validate_Interface $validator, $breakChainOnFailure = false);
}