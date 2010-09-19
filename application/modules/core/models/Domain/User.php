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
use \Xboom\Model\Domain\AbstractObject,
    \Doctrine\Common\Collections\ArrayCollection;
/**
 * @Entity
 * @Table(name="users")
 */
class User extends AbstractRole implements \Zend_Acl_Resource_Interface
{

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @Column(type="string", unique=true, length=50) */
    protected $name;

    /** @Column(type="string", unique=true, length=255) */
    protected $email;

    /** @Column(type="string", length=48) */
    protected $password;

    /**
     *
     * @ManyToOne(targetEntity="Group")
     * @var Group
     */
    protected $group = null;

    /**
     * Personal role, assigned to this user.
     *
     * @ManyToOne(targetEntity="Role")
     * @var Role
     */
    //protected $role = null;

    /**
     * Related resource.
     *
     * @OneToOne(targetEntity="Resource", cascade={"persist", "remove"})
     * @var Resourse
     */
    protected $resource = null;

    /**
     * Retrieve a list of all roles as array.
     * Check that would "null" missed the list, because is a reserved value
     *
     * @return array
     */
    public function  getRoleId()
    {
        if (null !== $this->getId())
        {
            return $this->getAllRoles();
        }

        return null;
    }

    /**
     *
     * @return array of Role objects
     */
    public function getAllRoles()
    {
        $roles = array();
        if (null !== $this->getGroup())
        {
            $roles = $this->getGroup()->getRoleId();
        }

//        // last element has a higher priority
//        if (null !== $this->getRole())
//        {
//            $roles[] = $this->getRole();
//        }

        return $roles;
    }

//    public function setRole($role)
//    {
//        if (null !== $role)
//        {
//            $role->markPersonal(true);
//            $this->role = $role;
//        }
//
//        return $this;
//    }

    /**
     * Returns the string identifier of the Resource
     *
     * @return string
     */
    public function  getResourceId()
    {
        if (null !== $this->getResource())
        {
            return $this->_getObjectName() . '-' . $this->resource->getId();
        }

        throw new \Xboom\Model\Exception('Resource don\'t assign');
    }

    /**
     *
     * @param Resource $resource
     */
    public function setResource($resource)
    {
        if (! ($resource instanceof Resource))
        {
            throw new \InvalidArgumentException('Resource must be a object');
        }

        $this->resource = $resource;
    }
}