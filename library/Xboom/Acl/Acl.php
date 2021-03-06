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
 * Access Control List
 *
 * @author yugeon
 */

namespace Xboom\Acl;

class Acl extends \Zend_Acl
{

    protected $_user;
    
    /**
     * {@inheritdoc}
     * 
     * Override, can add array by way of roles.
     *
     * @param string|array|Zend_Acl_Role_Interface $roles
     * @param string $parents
     * @return Acl
     */
    public function addRole($roles, $parents = null)
    {
        if (null !== $parents)
        {
            throw new \InvalidArgumentException('Inheritance prohibited');
        }

        if (\is_array($roles))
        {
            foreach ($roles as $role)
            {
                parent::addRole($role);
            }
            return $this;
        }
        else
        {
            parent::addRole($roles);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * Override, can checking array of roles.
     * As only meet allowed permission this function stop the checking and return true.
     * Otherwise always return false.
     *
     * @see parent::isAllowed()
     * @param Zend_Acl_Role_Interface|string|array $roles
     * @param Zend_Acl_Resource_Interface|string $resource
     * @param string $privilege
     * @param User $user
     * @return boolean
     */
    public function isAllowed($roles = null, $resource = null, $privilege = null, $user = null)
    {
        $isAllow = false;
        
        // Clear verifiable user.
        $this->_user = null;
        if (null !== $user)
        {
            // remember verifiable user.
            $this->_user = $user;
        }

        if ($roles instanceof \Zend_Acl_Role_Interface)
        {
            $roles = $roles->getRoleId();
        }

        if (\is_array($roles))
        {
            foreach ($roles as $role)
            {
                $isAllow = $this->_isAllowed($role, $resource, $privilege);

                if ($isAllow)
                {
                    break;
                }
            }
        }
        else
        {
            $isAllow = $this->_isAllowed($roles, $resource, $privilege);
        }

        return $isAllow;
    }

    protected function _isAllowed($role = null, $resource = null, $privilege = null)
    {
        $isAllow = false;
        if (null !== $role && !parent::hasRole($role))
        {
            $isAllow = false;
        }
        elseif (null !== $resource && !parent::has($resource))
        {
            $isAllow = false;
        }
        else
        {
            $isAllow = parent::isAllowed($role, $resource, $privilege);
        }
        return $isAllow;
    }

    public function getRememberedUser()
    {
        return $this->_user;
    }
}
