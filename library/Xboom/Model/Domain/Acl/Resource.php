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
 * Abstract Resource
 *
 * @author yugeon
 */
namespace Xboom\Model\Domain\Acl;
use \Xboom\Model\Domain\AbstractObject,
    \Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="resources")
 */
class Resource extends AbstractObject implements \Zend_Acl_Resource_Interface
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     *
     * @var integer
     */
    protected $id;

    /**
     * Simple name for this resource.
     *
     * @Column(length=50)
     * @var string
     */
    protected $name;

    /**
     * Current nesting level.
     * Needed to quickly build a list of the resource hierarchy.
     *
     * @Column(type="integer")
     * @var int
     */
    protected $level = 0;

    /**
     *
     * @ManyToOne(targetEntity="Resource")
     * @var Resource
     */
    protected $parent;

    /**
     * Resource owner.
     *
     * @ManyToOne(targetEntity="\Core\Model\Domain\User")
     * @var User
     */
    protected $owner = null;

    /**
     * @OneToMany(targetEntity="Permission", mappedBy="resource")
     * @var Permission
     */
    protected $permissions = null;

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

    public function  getResourceId()
    {
        return (string) $this->name;
    }

    /**
     * Set parent resource. It increases the count of nesting level.
     *
     * @param Resource $parent
     * @return Resource
     */
    public function setParent($parent)
    {
        $parentLevel = $parent->getLevel();
        $this->setLevel($parentLevel + 1);
        $this->parent = $parent;
        return $this;
    }

    /**
     * Recursively gets all the parents.
     *
     * @return array
     */
    public function getAllParents()
    {
        $result = array();
        $parentResource = $this->getParent();
        if (null !== $parentResource)
        {
            $result = $parentResource->getAllParents();
            $result[] = $this->getParent();
        }
        return $result;
    }
}
