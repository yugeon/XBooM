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
 * Provides service layer to the navigation.
 *
 * @author yugeon
 */
namespace App\Core\Model\Service;

use \Xboom\Model\Service\Exception as ServiceException;

class NavigationService
{

    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $_em = null;

    /**
     *
     * @var array of Menu
     */
    protected $_menus = array();

    protected $_menuClass = '';
    protected $_pageClass = '';

    public function __construct($em)
    {
        $this->_em = $em;
    }

    /**
     *
     * @param string $menuClass
     * @return NavigationService
     */
    public function setMenuClass($menuClass)
    {
        $this->_menuClass = $menuClass;
        return $this;
    }

    /**
     *
     * @param string $pageClass
     * @return NavigationService
     */
    public function setPageClass($pageClass)
    {
        $this->_pageClass = $pageClass;
        return $this;
    }

    /**
     * Get navigation
     *
     * @param string $name
     * @return Zend_Navigation_Container
     */
    public function getNavigation($name = 'default')
    {
        $navigation = $this->getNavigationAsArray($name);
        return new \Zend_Navigation($navigation);
    }

    /**
     *
     * @param string $name
     * @return Menu|null
     */
    public function getMenu($name)
    {
        if (!isset ($this->_menus[$name]))
        {
            // TODO caching
            $menu = $this->_buildMenuByName($name);
            $this->setMenu($menu, $name);
        }

        return $this->_menus[$name];
    }
    /**
     * Save the menu object in internal cache.
     *
     * @param Menu $menu
     * @param string $name
     * @return NavigationService
     */
    public function setMenu($menu, $name = 'default')
    {
        $this->_menus[$name] = $menu;
        return $this;
    }

    /**
     * Reset menu object from internal cache.
     *
     * @param string $name Menu name.
     * @return NavigationService
     */
    public function unsetMenu($name)
    {
        if (isset($this->_menus[$name]))
        {
            unset($this->_menus[$name]);
        }
        return $this;
    }

    /**
     * Transform menu domain object to navigation array.
     *
     * @param string $name
     * @return array
     */
    public function getNavigationAsArray($name = 'default')
    {
        $navigation = array();
        $menu = $this->getMenu($name);
        if (!\is_null($menu))
        {
            $navigation = $this->_extractPages($menu->getPages());
        }
        
        return $navigation;
    }

    /**
     * Build a menu object with all its pages.
     *
     * @param string $name
     * @return Menu|null
     */
    protected function _buildMenuByName($name)
    {
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->_em->createQueryBuilder();

        $qb->select(array('menu', 'page', 'child', 'res'))
                ->from($this->_menuClass, 'menu')
                ->leftJoin('menu.pages', 'page')
                ->leftJoin('page.pages', 'child')
                ->leftJoin('page.resource', 'res')
                ->where('menu.name =?1')
                ->orderBy('page.order')
                ->setParameter(1, $name);

        $query = $qb->getQuery();
        $result = $query->getResult();

        $menu = null;
        if (!empty($result))
        {
            $menu = $result[0];
        }
        return $menu;
    }

    /**
     * Recursive extract pages for navigation container.
     *
     * @param Array $pages
     * @return Array
     */
    public function _extractPages($pages)
    {
        $result = array();

        foreach ($pages as $index => $page)
        {
            foreach ($page->toArray() as $key => $value)
            {   
                if (!empty($value))
                {
                    if ('parent' == $key)
                    {
                        continue;
                    }

                    if ('resource' == $key)
                    {
                        $result[$index]['resource'] = $value->getResourceId();
                        continue;
                    }

                    if ('pages' == $key)
                    {
                        $result[$index]['pages'] = $this->_extractPages($value->toArray());
                        continue;
                    }
                    
                    $result[$index][$key] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * Returns the unique identifiers of those pages
     * that are involved in a save operation
     *
     * @param array $menuHierarchy
     * @return array
     */
    protected function _normalizeMenuHierarchyIds($menuHierarchy)
    {
        $ids = array();
        foreach ($menuHierarchy as $pageItem)
        {
            $ids[] = $pageItem['id'];
            $ids[] = $pageItem['parent'];
        }
        return \array_unique($ids);
    }

    /**
     * Retrieve all pages with relevant ids.
     *
     * @param array $ids Page idnetificators.
     * @return array
     */
    protected function _getPagesByIds(array $ids)
    {
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->_em->createQueryBuilder();
        $qb->select(array('page'))
                ->from($this->_pageClass, 'page')
                ->leftJoin('page.parent', 'p')
                ->where($qb->expr()->in('page.id', $ids));
        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result;
    }

    /**
     * Retrieve page object from selection.
     *
     * @param array $pages
     * @param int $id
     * @return Page|null
     */
    protected function _getPageById($pages, $id)
    {
        foreach ($pages as $page)
        {
            if ($id == $page->getId())
            {
                return $page;
            }
        }
        return null;
    }

    /**
     * Save menu hierarchy.
     *
     * @param array $data
     * @return NavigationService
     * @throws ServiceException
     */
    public function saveMenuHierarchy($data)
    {
        // TODO ACL
        // TODO Validation
        try
        {
            $menuName = $data['menuName'];
            $menuHierarchy = $data['data'];

            $menu = $this->_em->getRepository($this->_menuClass)->findOneByName($menuName);

            $ids = $this->_normalizeMenuHierarchyIds($menuHierarchy);
            $pages = $this->_getPagesByIds($ids, $menuName);

            foreach ($menuHierarchy as $menuItem)
            {
                $id = $menuItem['id'];
                $parentId = $menuItem['parent'];
                $order = $menuItem['order'];

                $page = $this->_getPageById($pages, $id);
                if (!\is_null($page))
                {
                    $page->setOrder($order);
                    $newParent = $this->_getPageById($pages, $parentId);
                    if (\is_null($newParent))
                    {
                        // assign page to menu
                        $page->removeParent();
                        $menu->assignToPage($page);
                    }
                    else
                    {
                        // change parent
                        if (\is_null($page->getParent()))
                        {
                            // remove from the menu, as this page is now a subordinate
                            $menu->removePage($page);
                        }
                        $page->changeParent($newParent);
                    }
                }
            }

            $this->_em->flush();
            return $this;
        }
        catch(\Exception $e)
        {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
