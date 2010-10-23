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

class NavigationService
{

    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $_em = null;

    /**
     *
     * @var array of Zend_Navigation_Container
     */
    protected $_navigations = array();

    protected $_menuClass = '';

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
     * Get navigation
     *
     * @param string $name
     * @return Zend_Navigation_Container
     */
    public function getNavigation($name = 'default')
    {
        if (!isset($this->_navigations[$name]))
        {
            // TODO caching
            $navigation = $this->_buildNavigationByName($name);
            $this->setNavigation($navigation, $name);
        }
        return new \Zend_Navigation($this->_navigations[$name]);
    }

    /**
     *
     * @param Zend_Navigation_Container $navigation
     * @param string $name
     * @return NavigationService
     */
    public function setNavigation($navigation, $name = 'default')
    {
        $this->_navigations[$name] = $navigation;
        return $this;
    }

    public function unsetNavigation($name)
    {
        if (isset($this->_navigations[$name]))
        {
            unset($this->_navigations[$name]);
        }
        return $this;
    }

    /**
     *
     * @param string $name
     * @return array
     */
    public function getNavigationAsArray($name = 'default')
    {
        if (!isset($this->_navigations[$name]))
        {
            // TODO caching
            $navigation = $this->_buildNavigationByName($name);
            $this->setNavigation($navigation, $name);
        }
        return $this->_navigations[$name];
    }

    /**
     *
     * @param string $name
     * @return array
     */
    protected function _buildNavigationByName($name)
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

        $pages = array();
        if (!empty($result))
        {
            $menu = $result[0];
            $pages = $this->_extractPages($menu->getPages());
        }

        return $pages;
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
    
    public function saveMenuHierarchy($menuHierarchy)
    {
        //$menuHierarchy = $this->_normalizeMenuHierarchy($menuHierarchy);
        // 1. Выбрать всю ветку
        // 2. Изменить коллекции
        // 3. Сохранить
        return $this;
    }
}
