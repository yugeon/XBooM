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

use \Xboom\Model\Domain\DomainObject,
 \Xboom\Model\Domain\Acl\Resource,
 \Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="Core\Model\Domain\Repository\UserRepository")
 * @Table(name="users")
 */
class User extends DomainObject implements \Zend_Acl_Role_Interface, \Zend_Acl_Resource_Interface
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

    /** @Column(type="string", nullable=true, length=255) */
    protected $password;

    /**
     *
     * @ManyToOne(targetEntity="Group")
     * @var Group
     */
    protected $group = null;

    /**
     * Related resource.
     *
     * @OneToOne(targetEntity="\Xboom\Model\Domain\Acl\Resource")
     * @var Resourse
     */
    protected $resource = null;

    /**
     * Retrieve a list of all roles as array.
     * Check that would "null" missed the list, because is a reserved value
     *
     * @return array
     */
    public function getRoleId()
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

        return $roles;
    }

    public function getAllRolesAsId()
    {
        $rolesId = array();
        $roles = $this->getAllRoles();
        foreach ($roles as $role)
        {
            $rolesId[] = $role->getRoleId();
        }

        return $rolesId;
    }

    /**
     * Returns the string identifier of the Resource
     *
     * @return string
     */
    public function getResourceId()
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
        if (!($resource instanceof \Zend_Acl_Resource_Interface))
        {
            throw new \InvalidArgumentException('Resource must be a object');
        }

        $this->resource = $resource;
    }

    /**
     * Encrypt password
     * 
     * @param string $password
     * @return string Encrupted password
     */
    public function encryptPassword($password)
    {
        // FIXME: make a more secure algorithm with salt
        return \md5($password);
    }

    /**
     *
     * @return array
     */
    public function getIdentity()
    {
        $identity = array(
            'id'    => $this->getId(),
            'name'  => $this->getName(),
            'email' => $this->getEmail(),
            'roles' => $this->getAllRolesAsId()
        );
        return new UserIdentity($identity);
    }

    /**
     * Register user.
     *
     * @param array $data
     * @return User
     */
    public function register($data)
    {
        $this->name     = $data['name'];
        $this->email    = $data['email'];
        $this->password = $this->encryptPassword($data['password']);
        
        return $this;
    }

    /**
     *
     * @param string $password
     * @return false|array
     */
    public function authenticate($password)
    {
        $identity = false;

        if ($this->encryptPassword($password) == $this->password)
        {
            $identity = $this->getIdentity();
        }
        return $identity;
    }

}