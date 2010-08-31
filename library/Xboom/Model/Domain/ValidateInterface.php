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
namespace Xboom\Model\Domain;
interface ValidateInterface
{
    /**
     * Set validate object
     *
     * @param $validator Xboom\Model\Validate\ValidatorInterface
     * @return ValidateInterface for fluent.
     */
    public function setValidator($validator);

    /**
     * Validate Domain Object. Object without validators is default valid.
     *
     * If $data not present, then use values from current Domain Object.
     * Otherwise, use the data, assigned by key with properties name.
     * If $data fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  array $data
     * @return boolean
     * @throws \InvalidArgumentException if $data is not array
     * @throws Xboom\Validate\Exception If validation of $data is impossible
     */
    public function isValid($data = null);

    /**
     * Returns an array of messages that explain why the most recent isValid()
     * call returned false. The array keys are validation failure message identifiers,
     * and the array values are the corresponding human-readable message strings.
     *
     * If isValid() was never called or if the most recent isValid() call
     * returned true, then this method returns an empty array.
     *
     * @return array
     */
    public function getMessages();
}