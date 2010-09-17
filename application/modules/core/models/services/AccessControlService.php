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
    public function getAcl($currentUser)
    {
        if (isset($this->_acl[$currentUser->getId()]))
        {
            return $this->_acl[$currentUser->getId()];
        }

        $acl = $this->buildAcl($currentUser);
        $this->setAcl($acl, $currentUser);
        
        return $this->_acl[$currentUser->getId()];
    }

    public function setAcl($acl, $currentUser)
    {
        if (! ($acl instanceof \Zend_Acl) )
        {
            throw new \InvalidArgumentException('Acl must be instance of Zend_Acl');
        }

        $this->_acl[$currentUser->getId()] = $acl;

        return $this;
    }

    public function  buildAcl($user = null, $resource = null)
    {
        $acl = new Acl();

        $permissionClass = '\\Core\\Model\\Domain\\Permission';
        $userClass       = '\\Core\\Model\\Domain\\User';
        $userGroupClass  = '\\Core\\Model\\Domain\\Group';
        $roleClass  = '\\Core\\Model\\Domain\\Role';
        $resourceClass   = '\\Core\\Model\\Domain\\Resource';

        // PERMISSIONS
//        $query = $this->_em->createQuery(
//                "SELECT DISTINCT p, r FROM {$permissionClass} p"
//                . " LEFT JOIN p.resource r"
//                . " WHERE p.id IN (SELECT p1.id FROM {$userClass} u JOIN u.permissions p1 WHERE u.id = ?1)"
//                . " OR p.id IN (SELECT p2.id FROM {$userClass} u1 JOIN u1.groups g JOIN g.permissions p2 WHERE u1.id = ?1)"
//        );
//        $query = $this->_em->createQuery(
//                "SELECT p, (SELECT p1 FROM {$userClass} u JOIN u.permissions p1 WHERE u.id = ?1) as p2 FROM {$permissionClass} p"
               // . " LEFT JOIN p2.resource r"
//                . " WHERE p.id IN (SELECT p1.id FROM {$userClass} u JOIN u.permissions p1 WHERE u.id = ?1)"
//                . " OR p.id IN (SELECT p2.id FROM {$userClass} u1 JOIN u1.groups g JOIN g.permissions p2 WHERE u1.id = ?1)"
//        );

        // ROLES
//        $query = $this->_em->createQuery(
//                "SELECT r FROM {$roleClass} r"
//                //. " LEFT JOIN u.group g"
//                //. " LEFT JOIN g.roles r"
//                //. " WHERE u.id = ?1"
//        );
        $query = $this->_em->createQuery(
                 "SELECT u, g, r1, r2, p1, p2, res1, res2  FROM {$userClass} u"
               . ' LEFT JOIN u.group g'
               . ' LEFT JOIN u.role r1'
               . ' LEFT JOIN g.roles r2'
               . ' LEFT JOIN r1.permissions p1'
               . ' LEFT JOIN r2.permissions p2'
               . ' LEFT JOIN p1.resource res1'
               . ' LEFT JOIN p2.resource res2'
               . ' WHERE u.id = ?1'
        );
        $query->setParameter(1, 1/*$user->getId()*/);
        $fullUser = $query->getSingleResult();
//        $query->setParameter(1, 4/*$user->getId()*/);
//        $fullUser = $query->getSingleResult();
        
//        \Doctrine\Common\Util\Debug::dump($fullUser, 10);
//        exit;
        foreach ($fullUser->getAllRoles() as $role)
        {
            if (!$acl->hasRole($role))
            {
                $acl->addRole($role);
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

                if (\Core\Model\Domain\Permission::ALLOW === $permission->getType())
                {
                    $acl->allow($role, $resource, $permission->getName());
                }
                else
                {
                    $acl->deny($role, $resource, $permission->getName());
                }
            }
        }
                            
        return $acl;
    }
}
