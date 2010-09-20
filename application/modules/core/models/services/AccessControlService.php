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
 * Description of AccessControlService
 *
 * @author yugeon
 */

namespace Core\Model\Service;

use \Xboom\Model\Service\AbstractService,
 \Core\Model\Domain\Acl;

class AccessControlService extends AbstractService
{

    protected $_acl = array();

    public function __construct($em)
    {
        $this->_em = $em;
    }

    /**
     * Get a unique key for user and resource.
     * 
     * @param User|int|string $user 
     * @param Resource|int|string $resource
     * @param Permission|int|string $permission
     * @return string
     */
    public function getAclId($user = null, $resource = null, $permission = null)
    {
        $user = (null === $user)? 'full' : $user;
        $resource = (null === $resource)? 'full' : $resource;
        $permission = (null === $permission)? 'full' : $permission;

        $aclId = $user;
        if (\is_object($user))
        {
            $aclId = (string) $user->getId();
        }

        $aclId .= '::';

        if (\is_object($resource))
        {
            $aclId .= (string) $resource->getId();
        }
        else
        {
            $aclId .= (string) $resource;
        }

        $aclId .= '::';

        if (\is_object($permission))
        {
            $aclId .= (string) $permission->getId();
        }
        else
        {
            $aclId .= (string) $permission;
        }

        return \md5($aclId);
    }

    /**
     * Retrieve ACL for $user.
     *
     * @param User|string|int $user
     * @return Acl
     */
    public function getAcl($user = null, $resource = null, $permission = null)
    {
        $aclId = $this->getAclId($user, $resource, $permission);

        if (isset($this->_acl[$aclId]))
        {
            return $this->_acl[$aclId];
        }

        // TODO Add caching

        $acl = $this->buildAcl($user, $resource, $permission);
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
     * @param User|int|null $user
     * @param Resource|int|null $resource
     * @param Permission|int|string|null
     * @return Acl
     */
    public function buildAcl($user = null, $resource = null, $permission = null)
    {
        $acl = new Acl();

        if (\is_string($user) && $user === 'guest')
        {
            return $this->_getGuestAcl($acl);
        }

        $userClass = '\\Core\\Model\\Domain\\User';

        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->_em->createQueryBuilder();
        $qb->select(array('u', 'g', 'r', 'p', 'res'))
                ->from($userClass, 'u')
                ->leftJoin('u.group', 'g')
                ->leftJoin('g.roles', 'r')
                ->leftJoin('r.permissions', 'p')
                ->leftJoin('p.resource', 'res');

        // constraint by user
        if (null !== $user)
        {
            $userId = $user;
            if (\is_object($user))
            {
                $userId = $user->getId();
            }

            if (\is_int($userId))
            {
                $qb->where($qb->expr()->eq('u.id', '?1'))
                        ->setParameter(1, $userId);
            }
        }

        // constraint by resource
        if (null !== $resource)
        {
            // Когда запрос по конкретному ресурсу, то необходимо так же
            // подгружать все роли и привилегии для родительских ресурсов
            // Для этого надо знать все id родительских ресурсов.

            $resIds = $this->_getResourceParentsId($resource);
            if (empty($resIds))
            {
                return $acl;
            }
            $qb->andWhere($qb->expr()->in('res.id', $resIds));

        }

        // constraint by permission
        if (null !== $permission)
        {
            $permissionId = $permission;
            if (\is_object($permission))
            {
                $permissionId = $permission->getId();
            }

            if (\is_int($permissionId))
            {
                $qb->andWhere($qb->expr()->eq('p.id', '?3'))
                        ->setParameter(3, $permissionId);
            }
        }

        $query = $qb->getQuery();
        $users = $query->getResult();
        // var_dump($qb->getQuery()->getSQL());
        //\Doctrine\Common\Util\Debug::dump($users, 50);
        //exit;

        foreach ($users as $user)
        {
            $roles = $user->getAllRoles();
            $this->_extractRolesPermissionsAndResources($acl, $roles);
        }

        return $acl;
    }

    /**
     * Get all the ids of parents resources by single query without recursion.
     *
     * @param Resource|int|string $resource
     * @return array
     */
    protected function _getResourceParentsId($resource)
    {
        $result = array();

        $resourceClass = '\\Core\\Model\\Domain\\Resource';
        if (\is_int($resource))
        {
            $resource = $this->_em->find($resourceClass, $resource);
        }
        elseif (\is_string($resource))
        {
            $resource = $this->_em->getRepository($resourceClass)->findOneByName( $resource);
        }

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
           ->from($resourceClass, 'r0')
           ->leftJoin('r0.parent', 'r1')
           ->where($qb->expr()->eq('r0.id', '?1'))
           ->setParameter(1, $resourceId);

        for ($i = 1; $i < $currentLevel; $i++)
        {
            $qb->addSelect('r' . ($i+1) . '.id r' . ($i+1) . '_id');
            $qb->leftJoin('r' . $i . '.parent', 'r' . ($i+1));
        }

        $query = $qb->getQuery();
        $result = $query->getArrayResult();

//        var_dump($qb->getDQL());
//        var_dump($qb->getQuery()->getSQL());
//        \Doctrine\Common\Util\Debug::dump($result, 50);
//        exit;

        return $result[0];
    }

    protected function _extractRolesPermissionsAndResources($acl, $roles)
    {
        foreach ($roles as $role)
        {
            if (!$acl->hasRole($role->getRoleId()))
            {
                $acl->addRole($role->getRoleId());
            }

            foreach ($role->getPermissions() as $permission)
            {
                $resource = $permission->getResource();
                if (null === $resource)
                {
                    // Resource must have.
                    continue;
                }
                $this->_addResource($acl, $resource);

                // TODO asserting by owner
                if (\Core\Model\Domain\Permission::ALLOW === $permission->getType())
                {
                    $acl->allow($role->getRoleId(), $resource, $permission->getName());
                }
                else
                {
                    $acl->deny($role->getRoleId(), $resource, $permission->getName());
                }
            }
        }
    }

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

    protected function _getGuestAcl($acl)
    {
        $groupClass = '\\Core\\Model\\Domain\\Group';
        $dql = "SELECT g, r, p, res FROM {$groupClass} as g"
                . ' LEFT JOIN g.roles as r'
                . ' LEFT JOIN r.permissions as p'
                . ' LEFT JOIN p.resource as res'
                . ' WHERE g.id = :guest_group_id';
        $query = $this->_em->createQuery($dql)
                        // FIXME Решить как идентифицировать группу для Гостей.
                        ->setParameter('guest_group_id', 1);
        $group = $query->getResult();

        if (!empty($group[0]))
        {
            $roles = $group[0]->getAllRoles();
            $this->_extractRolesPermissionsAndResources($acl, $roles);
        }

        return $acl;
    }

}
