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

namespace Core\Model\Domain;
/**
 * @Entity
 * @Table(name="users")
 */
class User
    extends \Xboom\Model\Domain\AbstractObject
    implements \Zend_Acl_Role_Interface
{

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @Column(type="string", length=50) */
    protected $name;

    /** @Column(type="string", length=16) */
    protected $login;

    /** @Column(type="string", length=32) */
    protected $password;

    protected $role = 'guest';


    public function __construct(array $data = null)
    {

        if (!is_null($data))
        {
            // Login field must be set
            if (empty($data['login']))
            {
                throw new \InvalidArgumentException('Login must be set.');
            }

            // If name not set, then name equals login
            if (empty($data['name']))
            {
                $data['name'] = $data['login'];
            }

            foreach ($data as $property => $value)
            {
                $accessor = 'set' . ucfirst($property);
                $this->{$accessor}($value);
            }
        }
    }

    /**
     * Returns the string identifier of the Role
     *
     * @return string
     */
    public function getRoleId()
    {
        return $this->role;
    }

}