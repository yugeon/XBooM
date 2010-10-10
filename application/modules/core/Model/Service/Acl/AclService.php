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
 * Description of AclService
 *
 * @author yugeon
 */

namespace App\Core\Model\Service\Acl;

use \Xboom\Model\Service\AbstractService,
 \App\Core\Model\Domain\Acl\Permission,
 \App\Core\Model\Domain\Acl\Resource,
 \App\Core\Model\Domain\Acl\Role,
 \Xboom\Acl\Acl;

class AclService
{

    protected $_em = null;
    protected $_acl = array();
    protected $_assertions = array();
    protected $_resourceClass = '';

    public function __construct($em)
    {
        $this->_em = $em;
    }

    /**
     *
     * @param string $resourceClass
     * @return AclService 
     */
    public function setResourceClass($resourceClass)
    {
        $this->_resourceClass = $resourceClass;
        return $this;
    }

    public function getAssertion($assertionName)
    {
        if (!isset($this->_assertions[$assertionName]))
        {
            $assertionClass = "\\Xboom\\Acl\\Assert\\Is{$assertionName}Assertion";
            $assertionObject = new $assertionClass;
            $this->setAssertion($assertionName, $assertionObject);
        }

        return $this->_assertions[$assertionName];
    }

    public function setAssertion($assertionName, $assertion)
    {
        if (!($assertion instanceof \Zend_Acl_Assert_Interface))
        {
            throw new \InvalidArgumentException(
                    'Assertion must be implement of Zend_Acl_Assert_Interface');
        }

        $this->_assertions[$assertionName] = $assertion;
        return $this;
    }

    /**
     * Get a unique key for role, resource and permission.
     *
     * @param array $roleId
     * @param string $resourceId
     * @param string $permissionId
     * @return string
     */
    protected function _getAclId($roleId = array(), $resourceId = null, $permissionId = null)
    {
        $aclId = 'all';
        foreach ($roleId as $value)
        {
            $aclId .= '::' . $value;
        }

        $aclId = $aclId . '::' . $resourceId . '::' . $permissionId;
        return \md5($aclId);
    }

    /**
     * Normalize the role id.
     *
     * @param Zend_Acl_Role_Interface|int $role
     * @return int
     */
    protected function _normalizeSingleRoleId($role = null)
    {
        $roleId = null;
        if ($role instanceof \Zend_Acl_Role_Interface)
        {
            $roleId = (int) $role->getRoleId();
        }
        elseif (\is_int($role) || \is_string($role))
        {
            $roleId = (int) $role;
        }

        return $roleId;
    }

    /**
     *
     * @param array|Zend_Acl_Role_Interface|int $roleId
     * @return array
     */
    public function _normalizeRoleId($roleId)
    {
        $result = array();

        if (\is_array($roleId))
        {
            foreach ($roleId as $value)
            {
                $normValue = $this->_normalizeSingleRoleId($value);
                if (null !== $normValue)
                {
                    $result[] = $normValue;
                }
            }
        }
        else
        {
            $normValue = $this->_normalizeSingleRoleId($roleId);
            if (null !== $normValue)
            {
                $result[] = $normValue;
            }
        }

        return $result;
    }

    /**
     * Normalize the resource id.
     *
     * @param Zend_Acl_Resource_Interface|string $resource
     * @return string
     */
    protected function _normalizeResourceId($resource = 'all')
    {
        $resourceId = 'all';
        if ($resource instanceof \Zend_Acl_Resource_Interface)
        {
            $resourceId = (string) $resource->getResourceId();
        }
        elseif (\is_string($resource))
        {
            $resourceId = $resource;
        }
        return $resourceId;
    }

    /**
     * Normalize the permission id.
     *
     * @param Permission|string $permission
     * @return string
     */
    protected function _normalizePermissionId($permission = null)
    {
        $permissionId = 'all';
        if ($permission instanceof Permission)
        {
            $permissionId = (string) $permission->getName();
        }
        elseif (\is_string($permission))
        {
            $permissionId = (string) $permission;
        }
        return $permissionId;
    }

    /**
     * Retrieve ACL for $user.
     *
     * @param Zend_Acl_Role_Inreface|int|array $role
     * @param Zend_Acl_Resource_Inreface|string $resource
     * @param Permission|string $permission
     * @return Acl
     */
    public function getAcl($role = array(), $resource = null, $permission = null)
    {
        $roleId = $this->_normalizeRoleId($role);
        $resourceId = $this->_normalizeResourceId($resource);
        $permissionId = $this->_normalizePermissionId($permission);

        $aclId = $this->_getAclId($roleId, $resourceId, $permissionId);

        if (isset($this->_acl[$aclId]))
        {
            return $this->_acl[$aclId];
        }

        // TODO Add caching

        $acl = $this->buildAcl($roleId, $resourceId, $permissionId);
        $this->setAcl($acl, $aclId);

        return $this->_acl[$aclId];
    }

    public function setAcl($acl, $aclId)
    {
        if (!($acl instanceof \Zend_Acl))
        {
            throw new \InvalidArgumentException('Acl must be instance of Zend_Acl');
        }

        $this->_acl[$aclId] = $acl;

        return $this;
    }

    /**
     * Build full ACL. If $user is passed, build on to the $user.
     * If $resource is passed, build on to the resource.
     *
     * @param array $role
     * @param string    $resource
     * @param string    $permission
     * @return Acl
     */
    public function buildAcl($role, $resource, $permission)
    {
        $acl = new Acl();

        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->_em->createQueryBuilder();

        $qb->select(array('res', 'p', 'r', 'res_own'))
                ->from($this->_resourceClass, 'res')
                ->leftJoin('res.owner', 'res_own');

        // constraint by permission
        if ('all' !== $permission)
        {
            $qb->leftJoin('res.permissions', 'p', 'WITH', 'p.name = ?1')
                    ->setParameter(1, $permission);
        }
        else
        {
            $qb->leftJoin('res.permissions', 'p');
        }

        // constraint by role
        if (empty($role))
        {
            $qb->leftJoin('p.roles', 'r');
        }
        else
        {
            $qb->leftJoin('p.roles', 'r', 'WITH', $qb->expr()->in('r.id', $role));
        }

        // constraint by resource
        if ('all' !== $resource)
        {
            $resIds = $this->_getResourceParentsId($resource);
            if (empty($resIds))
            {
                // non-existent resource
                return $acl;
            }
            $qb->andWhere($qb->expr()->in('res.id', $resIds));
        }

        $query = $qb->getQuery();
        $resources = $query->getResult();
//        var_dump($qb->getDQL());
//        var_dump($query->getSQL());
//        \Doctrine\Common\Util\Debug::dump($resources, 6);
//        exit;

        $this->_extractResourcesRolesAndPermissions($acl, $resources);

        return $acl;
    }

    /**
     * Get all the ids of parents resources by single query without recursion.
     *
     * @param string $resourceName
     * @return array
     */
    protected function _getResourceParentsId($resourceName)
    {
        $result = array();

        $resource = $this->_em->getRepository($this->_resourceClass)->findOneByName($resourceName);

        if (!\is_object($resource))
        {
            return $result;
        }

        $resourceId = $resource->getId();
        $currentLevel = $resource->getLevel();
        $result = array($resourceId);
        if (0 >= $currentLevel)
        {
            return $result;
        }

        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->_em->createQueryBuilder();
        $qb->select(array('r0.id r0_id', 'r1.id r1_id'))
                ->from($this->_resourceClass, 'r0')
                ->leftJoin('r0.parent', 'r1')
                ->where($qb->expr()->eq('r0.id', '?1'))
                ->setParameter(1, $resourceId);

        for ($i = 1; $i < $currentLevel; $i++)
        {
            $qb->addSelect('r' . ($i + 1) . '.id r' . ($i + 1) . '_id');
            $qb->leftJoin('r' . $i . '.parent', 'r' . ($i + 1));
        }

        $query = $qb->getQuery();
        $result = $query->getArrayResult();

//        var_dump($qb->getDQL());
//        var_dump($qb->getQuery()->getSQL());
//        \Doctrine\Common\Util\Debug::dump($result, 5);
//        exit;

        return $result[0];
    }

    protected function _extractResourcesRolesAndPermissions($acl, $resources)
    {
        foreach ($resources as $resource)
        {
            if (null === $resource)
            {
                // Resource required.
                continue;
            }
            $this->_addResource($acl, $resource);

            foreach ($resource->getPermissions() as $permission)
            {
                $assertion = null;
                if ($permission->isOwnerRestriction())
                {
                    $assertion = $this->getAssertion('Owner');
                }

                foreach ($permission->getRoles() as $role)
                {
                    if (!$acl->hasRole($role->getRoleId()))
                    {
                        $acl->addRole($role->getRoleId());
                    }

                    if (Permission::ALLOW === $permission->getType())
                    {
                        $acl->allow($role->getRoleId(), $resource, $permission->getName(), $assertion);
                    }
                    else
                    {
                        $acl->deny($role->getRoleId(), $resource, $permission->getName());
                    }
                }
            }
        }
    }

    /**
     * Recursively adds resources and their parents
     *
     * @param Zend_Acl $acl
     * @param Resource $resource
     */
    protected function _addResource($acl, $resource)
    {
        if (!$acl->has($resource))
        {
            $parentResource = $resource->getParent();
            if (null !== $parentResource)
            {
                $this->_addResource($acl, $parentResource);
            }
            $acl->add($resource, $parentResource);
        }
    }

}