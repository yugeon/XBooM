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
class User extends AbstractObject// implements \Zend_Acl_Role_Interface
{

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @Column(type="string", length=50) */
    protected $name;

    /** @Column(type="string", unique=true, length=32) */
    protected $login;

    /** @Column(type="string", length=48) */
    protected $password;

    /**
     * Groups in which user is consists.
     * 
     * @ManyToMany(targetEntity="Group")
     * @JoinTable(name="users_groups",
     *      joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="group_id", referencedColumnName="id")}
     *      )
     *
     * @var ArrayCollection
     */
    protected $groups = null;

    /**
     * Personal role this user.
     * 
     * @ManyToOne(targetEntity="Role")
     * @var Role
     */
    protected $role = null;

    /**
     * @OneToOne(targetEntity="Resource")
     *
     * @var Resourse
     */
    protected $resource = null;

    /**
     * Default constructor.
     * If $data exist, then assign to properties by key.
     *
     * @param array $data
     */
    public function __construct(array $data = null)
    {
        $this->groups = new ArrayCollection();
        parent::__construct($data);
    }

    /**
     * Assign user to $group
     * 
     * @param object Group $group
     * @return User
     * @throws \InvalidArgumentException if $group is not object.
     */
    public function assignToGroup($group)
    {
        if (!\is_object($group))
        {
            throw new \InvalidArgumentException('Param must be object');
        }
        
        if (!$this->groups->contains($group))
        {
            $this->groups[] = $group;
        }

        return $this;
    }

    /**
     * Override the default set method, which would add a group, rather than rewriting.
     *
     * @param ArrayCollection $groups
     * @return User
     */
    public function  setGroups($groups)
    {
        foreach ($groups as $group)
        {
            $this->assignToGroup($group);
        }
        return $this;
    }

    public function getRoles()
    {
        $roles = array();
        foreach ($this->getGroups() as $group)
        {
            if (null !== $group)
            {
                $role = $group->getRole()->getRoleId();
                if (null !== $role)
                {
                    $roles[] = $role;
                }
            }
        }

        // last element has a higher priority
        if (null !== $this->getRole())
        {
            $roles[] = $this->getRole()->getRoleId();
        }
        
        return $roles;
    }
}