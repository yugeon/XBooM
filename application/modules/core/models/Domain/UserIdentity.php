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
 * Description of UserIdentity
 *
 * @author yugeon
 */
namespace Core\Model\Domain;
use \Xboom\Model\Domain\AbstractObject;

class UserIdentity extends AbstractObject implements \Zend_Acl_Role_Interface
{
    protected $id = null;
    protected $name = null;
    protected $email = null;
    protected $roles = array();

    /**
     * Returns the array of string identifier of the Role
     *
     * @return array
     */
    public function getRoleId()
    {
        return $this->getRoles();
    }
}
