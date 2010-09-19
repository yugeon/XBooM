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
     * Retrieve ACL for $user.
     *
     * @param User $currentUser
     * @return Acl
     */
    public function getAcl($currentUser = null, $currentResource = null)
    {
        //FIXME сделать уникальный ключ по пользователю и ресурсу.
        if (\is_string($currentUser) && $currentUser === 'guest')
        {
            $aclId = 'guest';
        }
        elseif (null === $currentUser)
        {
            $aclId = 'full';
        }
        elseif (\is_object($currentUser))
        {
            $aclId = $currentUser->getId();

        }
        else
        {
            $aclId = $currentUser;
        }

        if (isset($this->_acl[$aclId]))
        {
            return $this->_acl[$aclId];
        }

        // TODO Add caching
        $acl = $this->buildAcl($currentUser, $currentResource);
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
     * @return Acl
     */
    public function buildAcl($user = null, $resource = null)
    {
        $acl = new Acl();

        if (\is_string($user) && $user === 'guest')
        {
            return $this->_getGuestAcl($acl);
        }

        $userClass = '\\Core\\Model\\Domain\\User';

        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->_em->createQueryBuilder();
        //$qb->select(array('u', 'g', 'r', 'r1', 'p', 'p1', 'res', 'res1'))
        $qb->select(array('u', 'g', 'r', 'p', 'res'))
                ->from($userClass, 'u')
                ->leftJoin('u.group', 'g')
                ->leftJoin('g.roles', 'r')
                ->leftJoin('r.permissions', 'p')
                ->leftJoin('p.resource', 'res');
        //->leftJoin('u.role',  'r1')
        //->leftJoin('r1.permissions', 'p1')
        //->leftJoin('p1.resource', 'res1')
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
            $resourceId = $resource;
            if (\is_object($resource))
            {
                $resourceId = $resource->getId();
            }

            if (\is_int($resourceId))
            {
                $qb->andWhere(//$qb->expr()->orX(
                                $qb->expr()->eq('res.id', '?2')
                        //$qb->expr()->eq('res1.id', '?2')
                        )//)
                        ->setParameter(2, $resourceId);
            }
        }

        $query = $qb->getQuery();
        $users = $query->getResult();
        foreach ($users as $user)
        {
            $roles = $user->getAllRoles();
            $this->_extractRolesPermissionsAndResources($acl, $roles);
        }

        return $acl;
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
                if (!$acl->has($resource))
                {
                    $acl->add($resource);

                    // FIXME разобраться с вложенными ресурсами
//                $parentResource = null;
//                if (null !== $resource->getParent())
//                {
//                    $parentResource = (string)$resource->getParent()->getId();
//                    $acl->add($parentResource);
//                }
//                $acl->add((string)$resource->getId(), $parentResource);
                }

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
