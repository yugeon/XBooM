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
 * Represents one page of menu.
 *
 * @author yugeon
 */

namespace App\Core\Model\Domain\Navigation;

use \Xboom\Model\Domain\DomainObject,
    \Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="pages")
 */
class Page extends DomainObject
{

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     *
     * @var integer
     */
    protected $id;
    /**
     * Text label of the page.
     *
     * @Column(length=50)
     * @var string
     */
    protected $label = null;
    /**
     * Style class for this page (CSS)
     *
     * @Column(length=50, nullable=true)
     * @var string
     */
    protected $class = null;
    /**
     * A more descriptive title for this page
     *
     * @Column(nullable=true)
     * @var string
     */
    protected $title = null;
    /**
     *  This page's target (_blank, _self, _parent, _top and so on)
     *
     * @Column(length=20, nullable=true)
     * @var string
     */
    protected $target = null;
    /**
     * Type of page. (mvc or uri)
     *
     * @Column(length=3)
     * @var string
     */
    protected $type = 'uri';
    /**
     * Page order
     *
     * @Column(type="integer", name="_order", nullable=true)
     * @var int
     */
    protected $order = null;
    /**
     * Module name to use when assembling URL
     *
     * @Column(length=50, nullable=true)
     * @var string
     */
    protected $module;
    /**
     * Controller name to use when assembling URL
     *
     * @Column(length=50, nullable=true)
     * @var string
     */
    protected $controller;
    /**
     * Action name to use when assembling URL
     *
     * @Column(length=50, nullable=true)
     * @var string
     */
    protected $action;
    /**
     * Params to use when assembling URL
     *
     * @Column(type="array", nullable=true)
     * @var array
     */
    protected $params;
    /**
     * Route name to use when assembling URL
     *
     * @Column(length=50, nullable=true)
     * @var string
     */
    protected $route;
    /**
     * Whether params should be reset when assembling URL
     *
     * @Column(type="boolean", nullable=true)
     * @var boolean
     */
    protected $resetParams = true;
    /**
     * Page URI
     *
     * @Column(nullable=true)
     * @var string
     */
    protected $uri = null;
    /**
     * Whether this page should be considered active
     *
     * @Column(type="boolean")
     * @var boolean
     */
    protected $isActive = false;
    /**
     * Whether this page should be considered visible
     *
     * @Column(type="boolean")
     * @var boolean
     */
    protected $isVisible = true;
    /**
     *
     * @ManyToMany(targetEntity="Page")
     * @JoinTable(name="pages_pages",
     *      joinColumns={@JoinColumn(name="parent_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="child_id", referencedColumnName="id")}
     *      )
     * @var ArrayCollection
     */
    protected $pages = null;
    /**
     * ACL resource associated with this page
     *
     * @OneToOne(targetEntity="\App\Core\Model\Domain\Acl\Resource")
     * @var Resource
     */
    protected $resource = null;
    /**
     * ACL permission associated with this page
     *
     * @Column(length=50, nullable=true)
     * @var string
     */
    protected $privilege = null;

    /**
     * Default constructor.
     * If $data exist, then assign to properties by key.
     *
     * @param array $data
     */
    public function __construct(array $data = null)
    {
        $this->pages = new ArrayCollection;
        parent::__construct($data);
    }

    public function addChildPage($page)
    {
        if (!$this->pages->contains($page))
        {
            $this->pages->add($page);
        }

        return $this;
    }

    public function add($data)
    {
        $this->setLabel($data['label']);
        $this->setClass($data['class']);
        $this->setTitle($data['title']);
        $this->setTarget($data['target']);
        $this->setOrder($data['order']);
        if ('mvc' === $data['type'])
        {
            $this->setType($data['type']);
            $this->setUri(null);
            $this->setAction($data['action']);
            $this->setController($data['controller']);
            $this->setModule($data['module']);
            $this->setParams($data['params']);
            $this->setRoute($data['route']);
            $this->setResetParams($data['resetParams']);
        }
        else
        {
            $this->setType('uri');
            $this->setUri($data['uri']);
            $this->setAction(null);
            $this->setController(null);
            $this->setModule(null);
            $this->setParams(null);
            $this->setRoute(null);
        }
        if (!empty($data['resource']))
        {
            $this->setResource($data['resource']);
        }
        $this->setPrivilege($data['privilege']);
        if (null !== $data['isActive'])
        {
            $this->setIsActive($data['isActive']);
        }
        if (null !== $data['isVisible'])
        {
            $this->setIsVisible($data['isVisible']);
        }
        
        return $this;
    }

}
