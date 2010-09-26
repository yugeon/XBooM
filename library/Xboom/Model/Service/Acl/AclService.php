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
 * @todo refactoring
 */

namespace Xboom\Model\Service\Acl;

use \Xboom\Model\Service\AbstractService,
 \Xboom\Model\Domain\Acl\Permission,
 \Xboom\Acl\Acl;

class AclService //extends AbstractService
{

    protected $_em;
    protected $_acl = array();
    protected $_assertions = array();
    protected $resourceClass = '\\Xboom\\Model\\Domain\\Acl\\Resource';

    public function __construct($em)
    {
        $this->_em = $em;
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
     * Get a unique key for user and resource.
     * 
     * @param User|int|string $user
     * @param Resource|int|string $resource
     * @param Permission|int|string $permission
     * @return string
     */
    public function getAclId($user = null, $resource = null, $permission = null)
    {
        $user = (null === $user) ? 'all' : $user;
        $resource = (null === $resource) ? 'all' : $resource;
        $permission = (null === $permission) ? 'all' : $permission;

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

        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->_em->createQueryBuilder();

        $qb->select(array('res', 'p', 'r', 'res_own'))
                ->from($this->resourceClass, 'res')
                ->leftJoin('res.owner', 'res_own');

        // constraint by permission
        $permissionId = $permission;
        if (\is_object($permission))
        {
            $permissionId = $permission->getId();
        }

        if (\is_int($permissionId))
        {
            $qb->leftJoin('res.permissions', 'p', 'WITH', 'p.id = ?1')
                    ->setParameter(1, $permissionId);
        }
        elseif (\is_string($permissionId))
        {
            $qb->leftJoin('res.permissions', 'p', 'WITH', 'p.name = ?1')
                    ->setParameter(1, $permissionId);
        }
        else
        {
            $qb->leftJoin('res.permissions', 'p');
        }

        // constraint by user
        $userId = $user;
        if (\is_object($user))
        {
            $userId = $user->getId();
        }

        if (\is_int($userId))
        {
            $qb->leftJoin('p.roles', 'r', 'WITH',
                            'r.id IN (SELECT ur.id FROM \Core\Model\Domain\User u'
                            . ' JOIN u.group g JOIN g.roles ur WHERE u.id = :id)')
                    ->setParameter('id', $userId);
        }
        elseif (\is_string($userId))
        {
            if ('guest' === $userId)
            {
                $userId = 'guest@guest';
            }

            $qb->leftJoin('p.roles', 'r', 'WITH',
                            'r.id IN (SELECT ur.id FROM \Core\Model\Domain\User u'
                            . ' JOIN u.group g JOIN g.roles ur WHERE u.email = :email)')
                    ->setParameter('email', $userId);
        }
        else
        {
            $qb->leftJoin('p.roles', 'r');
        }

        // constraint by resource
        if (null !== $resource)
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
     * @param Resource|int|string $resource
     * @return array
     */
    protected function _getResourceParentsId($resource)
    {
        $result = array();

        if (\is_int($resource))
        {
            $resource = $this->_em->find($this->resourceClass, $resource);
        }
        elseif (\is_string($resource))
        {
            $resource = $this->_em->getRepository($this->resourceClass)->findOneByName($resource);
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
                ->from($this->resourceClass, 'r0')
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

    public function isAllowed($user, $resource, $permission)
    {
        $acl = $this->getAcl($user, $resource, $permission);
        return $acl->isAllowed($user, 'Concrete Resource', 'edit');
    }
}