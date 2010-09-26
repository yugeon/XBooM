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
 * Description of Group
 *
 * @author yugeon
 */
namespace Core\Model\Domain;
use \Xboom\Model\Domain\AbstractObject,
    \Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="groups")
 */
class Group extends AbstractObject implements \Zend_Acl_Role_Interface
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Name of group
     *
     * @var string
     * @Column(type="string", unique=true, length=100)
     */
    protected $name;

    /**
     * Description of this group.
     *
     * @Column(type="string", nullable=true, length=255)
     * @var string
     */
    protected $description;

    /**
     * All roles, assigned to this group.
     *
     * @ManyToMany(targetEntity="\Xboom\Model\Domain\Acl\Role")
     * @var ArrayCollection of Role
     */
    protected $roles = null;


    /**
     * Default constructor.
     * If $data exist, then assign to properties by key.
     *
     * @param array $data
     */
    public function __construct(array $data = null)
    {
        $this->roles = new ArrayCollection();
        parent::__construct($data);
    }

    /**
     * Returns an array of string identifiers of roles.
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

    public function getAllRoles()
    {
        return $this->getRoles()->toArray();
    }

    public function assignToRole($role)
    {
        if (!\is_object($role))
        {
            throw new \InvalidArgumentException('Param must be a Role object');
        }

        if (!$this->roles->contains($role))
        {
            $this->roles[] = $role;
        }

        return $this;
    }
}
