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
 * Represents a menu.
 *
 * @author yugeon
 */
namespace App\Core\Model\Domain\Navigation;
use \Xboom\Model\Domain\DomainObject,
    \Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="menus")
 */
class Menu extends DomainObject
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     *
     * @var integer
     */
    protected $id;
    /**
     *
     * @Column(unique=true, length=20)
     * @var string
     */
    protected $name;
    /**
     *
     * @Column(length=50, nullable=true)
     * @var string
     */
    protected $description;
    /**
     * @ManyToMany(targetEntity="Page")
     * @JoinTable(name="menus_pages",
     *      joinColumns={@JoinColumn(name="menu_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="page_id", referencedColumnName="id")}
     *      )
     * @var ArrayCollection
     */
    protected $pages = null;

    /**
     * Default constructor.
     * If $data exist, then assign to properties by key.
     *
     * @param array $data
     */
    public function __construct(array $data = null)
    {
        $this->pages = new ArrayCollection();
        parent::__construct($data);
    }

    /**
     *
     * @param Page $page
     * @return Menu
     */
    public function assignToPage($page)
    {
        if (!$this->pages->contains($page))
        {
            $this->pages->add($page);
        }

        return $this;
    }

    public function removePage($page)
    {
        $this->getPages()->removeElement($page);
        return $this;
    }

    public function add($data)
    {
        $this->setName($data['name']);
        if (!empty($data['description']))
        {
            $this->setDescription($data['description']);
        }
        return $this;
    }

}
