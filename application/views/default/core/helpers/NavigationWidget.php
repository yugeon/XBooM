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
 * NavigationWidget. Proxy for navigation helper.
 * Can work with distinct menu containers.
 *
 * @author yugeon
 */
class Core_View_Helper_NavigationWidget extends Zend_View_Helper_Abstract
{
    protected $navigationHelper;

    /**
     *
     * @param string $name Container name.
     * @return Core_View_Helper_NavigationWidget
     */
    public function navigationWidget($name = 'default')
    {
        if (!isset($this->view->serviceContainer)
                || !is_object($this->view->serviceContainer))
        {
            return;
        }

        // get navigation container
        $navService = $this->view->serviceContainer->getService('NavigationService');
        $container = $navService->getNavigation($name);

        // init navigation container
        $this->navigationHelper = $this->view->navigation($container);

        // get current user identity
        $authService = $this->view->serviceContainer->getService('AuthService');
        $curUserIdentity = $authService->getCurrentUserIdentity();

        if (!empty($curUserIdentity))
        {
            // get acl for current user
            $aclService = $this->view->serviceContainer->getService('AclService');
            $acl = $aclService->getAcl($curUserIdentity->getRoles());

            $this->navigationHelper->setAcl($acl);
            $this->navigationHelper->setRole($curUserIdentity);
        }

        return $this;
    }

    /**
     * Proxy calls to navigation helper.
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, array $arguments = array())
    {
        if (null !== $this->getNavigationHelper())
        {
            return call_user_func_array(array($this->getNavigationHelper(), $method), $arguments);
        }
        return $this;
    }

    public function getNavigationHelper()
    {
        return $this->navigationHelper;
    }
}
