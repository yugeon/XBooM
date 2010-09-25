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
 * Role.
 *
 * @category AccessControl
 * @author yugeon
 */
namespace Core\Model\Domain;
use \Xboom\Model\Domain\AbstractObject,
    \Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="roles")
 */
class Role extends AbstractObject implements \Zend_Acl_Role_Interface
{

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     *
     * @var integer
     */
    protected $id;

    /**
     * Simple name for this role.
     *
     * @Column(type="string", nullable=true, length=50)
     * @var string
     */
    protected $name;

    /**
     * Whether this is a personal role?
     *
     * @Column(type="boolean")
     * @var boolean
     */
    protected $isPersonal = false;

    /**
     * Description of this role.
     *
     * @Column(type="string", nullable=true, length=255)
     * @var string
     */
    protected $description;

    /**
     * Permissions assigned to this role. Owner side.
     *
     * @ManyToMany(targetEntity="Permission", inversedBy="roles")
     * @var ArrayCollection of Permission
     */
    protected $permissions;

    /**
     * Default constructor.
     * If $data exist, then assign to properties by key.
     *
     * @param array $data
     */
    public function __construct(array $data = null)
    {
        $this->permissions = new ArrayCollection();
        parent::__construct($data);
    }

    /**
     * Mark this role as personal
     *
     * @param boolean $flag
     * @return Role
     */
    public function markPersonal($flag)
    {
        $this->isPersonal = $flag;
        return $this;
    }

    /**
     * This is personal role?
     *
     * @return boolean
     */
    public function isPersonal()
    {
        return $this->isPersonal;
    }

    /**
     * Returns the string identifier of the Role
     *
     * @return string
     */
    public function  getRoleId()
    {
        return (string) $this->id;
    }

    public function assignToPermission($permission)
    {
        if (!\is_object($permission))
        {
            throw new \InvalidArgumentException('Param must be a Permission object');
        }

        if (!$this->permissions->contains($permission))
        {
            $this->permissions[] = $permission;
        }

        return $this;
    }

    /**
     * Override the default set method, which would add a permissions, rather than rewriting.
     *
     * @param ArrayCollection $permissions
     * @return Role 
     */
    public function setPermissions($permissions)
    {
        foreach ($permissions as $permission)
        {
            $this->assignToPermission($permission);
        }
        return $this;
    }
}
