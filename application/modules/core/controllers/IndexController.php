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
use \Core\Model\Domain\User,
    \Core\Model\Domain\Group,
    \Core\Model\Domain\Resource,
    \Core\Model\Domain\Permission;

class Core_IndexController extends Zend_Controller_Action
{
    /**
     *
     * @var Doctrine\ORM\EntityManager
     */
    protected  $em;

    public function init()
    {
        $sc = $this->getInvokeArg('bootstrap')->getContainer();
        $this->em = $sc->getService('doctrine.orm.entitymanager');
    }

    public function indexAction()
    {
    }

    public function initAction()
    {
        /*$user = new User();
        $user->name = 'fff' . rand(1,100);
        $user->login = $user->name;
        $user->password = md5($user->name);


        $group = new Group();
        $group->name = 'Group' . rand(1,100);
        $group1 = new Group();
        $group1->name = 'Group' . rand(1,100);

        $user->assignToGroup($group);
        $user->assignToGroup($group1);

        $this->em->persist($group);
        $this->em->persist($group1);
        $this->em->persist($user);
*/
        $group = $this->em->find('\\Core\\Model\\Domain\\User', 1);

     /*   $parentResource = new Resource();
        $parentResource->name = 'Parent'.rand(1,100);
        $resource = new Resource();
        $resource->name = 'Res'.rand(1,100);
        $resource->parent = $parentResource;

        $permission = new Permission();
        $permission->name = 'Permission' . rand(1,100);
        $permission->setResource($resource);
        $permission->setType(true);*/

        $permission = $this->em->find('\\Core\\Model\\Domain\\Permission', 2);
        $group->assignToPermission($permission);

//        $this->em->persist($parentResource);
  //      $this->em->persist($resource);
    //    $this->em->persist($permission);
        $this->em->flush();

     //   echo 'User ' . $user->id . ' succeful added';
        exit;
    }
}

