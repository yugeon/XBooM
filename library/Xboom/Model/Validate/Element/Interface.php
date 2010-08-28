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