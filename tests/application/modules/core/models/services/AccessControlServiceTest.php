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
 * Test case for AccessControlServiceTest
 *
 * @author yugeon
 */
namespace test\Core\Model\Service;

use \Core\Model\Service\AccessControlService,
    \Mockery as m;

class AccessControlServiceTest extends \PHPUnit_Framework_TestCase
{

    protected $object;
    protected $em;
    protected $currentUser;

    public function setUp()
    {
        $this->em = m::mock('\\Doctrine\\ORM\\EntityManager');
        $this->em->shouldReceive('persist');
        $this->em->shouldReceive('flush');

        $this->object = new AccessControlService($this->em);

        $this->currentUser = m::mock('User');
        $this->currentUser->shouldReceive('getId')->andReturn(232);
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testSetAcl()
    {
        $acl = m::mock('Zend_Acl');

        $this->object->setAcl($acl, 'guest');
        $this->assertEquals($acl, $this->object->getAcl('guest'));
    }

    public function testGetFullAcl()
    {
        $resource = m::mock('\\Zend_Acl_Resource_Interface');
        $resource->shouldReceive('getResourceId')->andReturn('test-resource-id');
        $permission = m::mock('Permission');
        $permission->shouldReceive('getResource')->andReturn($resource);
        $permission->shouldReceive('getName')->andReturn('test-permission-name');
        $permission->shouldReceive('getType')->andReturn(true);
        $permission1 = m::mock('Permission');
        $permission1->shouldReceive('getResource')->andReturn($resource);
        $permission1->shouldReceive('getName')->andReturn('test-permission-name1');
        $permission1->shouldReceive('getType')->andReturn(false);

        $permissions = array(
            $permission,
            $permission1
        );
        $role = m::mock('Role');
        $role->shouldReceive('getPermissions')->andReturn($permissions);
        $role->shouldReceive('getRoleId')->andReturn('test-role-id');
        $roles = array(
            $role,
            $role
        );
        $user = m::mock('User');
        $user->shouldReceive('getAllRoles')->andReturn($roles);
        $result = array(
            $user,
            $user
        );
        $query = m::mock('Query');
        $qb = m::mock('QueryBuilder');
        $qb->shouldReceive('select')->andReturn($qb);
        $qb->shouldReceive('from')->andReturn($qb);
        $qb->shouldReceive('leftJoin')->andReturn($qb);
        $qb->shouldReceive('where')->andReturn($qb);
        $qb->shouldReceive('getQuery')->andReturn($query);
        $this->em->shouldReceive('createQueryBuilder')->andReturn($qb);
        $query->shouldReceive('getResult')->andReturn($result);

        $this->assertNotNull($this->object->getAcl());
        $this->assertType('Zend_Acl', $this->object->getAcl());
        $this->assertTrue(
                $this->object->getAcl()
                ->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name'));
        $this->assertFalse(
                $this->object->getAcl()
                ->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name1'));
    }

    public function testGetAclByUser()
    {
        $resource = m::mock('\\Zend_Acl_Resource_Interface');
        $resource->shouldReceive('getResourceId')->andReturn('test-resource-id');
        $permission = m::mock('Permission');
        $permission->shouldReceive('getResource')->andReturn($resource);
        $permission->shouldReceive('getName')->andReturn('test-permission-name');
        $permission->shouldReceive('getType')->andReturn(true);
        $permission1 = m::mock('Permission');
        $permission1->shouldReceive('getResource')->andReturn($resource);
        $permission1->shouldReceive('getName')->andReturn('test-permission-name1');
        $permission1->shouldReceive('getType')->andReturn(false);

        $permissions = array(
            $permission,
            $permission1
        );
        $role = m::mock('Role');
        $role->shouldReceive('getPermissions')->andReturn($permissions);
        $role->shouldReceive('getRoleId')->andReturn('test-role-id');
        $roles = array(
            $role,
            $role
        );
        $user = m::mock('User');
        $user->shouldReceive('getId')->andReturn(2);
        $user->shouldReceive('getAllRoles')->andReturn($roles);
        $result = array(
            $user,
            $user
        );
        $query = m::mock('Query');
        $qb = m::mock('QueryBuilder');
        $qb->shouldReceive('select')->andReturn($qb);
        $qb->shouldReceive('from')->andReturn($qb);
        $qb->shouldReceive('leftJoin')->andReturn($qb);
        $qb->shouldReceive('where')->andReturn($qb);
        $qb->shouldReceive('andWhere')->andReturn($qb);
        $qb->shouldReceive('expr')->andReturn($qb);
        $qb->shouldReceive('eq')->andReturn($qb);
        $qb->shouldReceive('setParameter');
        $qb->shouldReceive('getQuery')->andReturn($query);
        $this->em->shouldReceive('createQueryBuilder')->andReturn($qb);
        $query->shouldReceive('getResult')->andReturn($result);

        $userId = 1;
        $this->assertNotNull($this->object->getAcl($userId));
        $this->assertType('Zend_Acl', $this->object->getAcl($userId));
        $this->assertTrue(
                $this->object->getAcl($userId)
                ->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name'));
        $this->assertFalse(
                $this->object->getAcl($userId)
                ->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name1'));

        $this->assertTrue(
                $this->object->getAcl($user)
                ->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name'));
        $this->assertFalse(
                $this->object->getAcl($user)
                ->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name1'));
    }

    public function testGetAclByResource()
    {
        $resource = m::mock('\\Zend_Acl_Resource_Interface');
        $resource->shouldReceive('getResourceId')->andReturn('test-resource-id');
        $resource->shouldReceive('getId')->andReturn(1);

        $permission = m::mock('Permission');
        $permission->shouldReceive('getResource')->andReturn($resource);
        $permission->shouldReceive('getName')->andReturn('test-permission-name');
        $permission->shouldReceive('getType')->andReturn(true);
        $permission1 = m::mock('Permission');
        $permission1->shouldReceive('getResource')->andReturn($resource);
        $permission1->shouldReceive('getName')->andReturn('test-permission-name1');
        $permission1->shouldReceive('getType')->andReturn(false);

        $permissions = array(
            $permission,
            $permission1
        );
        $role = m::mock('Role');
        $role->shouldReceive('getPermissions')->andReturn($permissions);
        $role->shouldReceive('getRoleId')->andReturn('test-role-id');
        $roles = array(
            $role,
            $role
        );
        $user = m::mock('User');
        $user->shouldReceive('getAllRoles')->andReturn($roles);
        $result = array(
            $user,
            $user
        );
        $query = m::mock('Query');
        $qb = m::mock('QueryBuilder');
        $qb->shouldReceive('select')->andReturn($qb);
        $qb->shouldReceive('from')->andReturn($qb);
        $qb->shouldReceive('leftJoin')->andReturn($qb);
        $qb->shouldReceive('where')->andReturn($qb);
        $qb->shouldReceive('andWhere')->andReturn($qb);
        $qb->shouldReceive('expr')->andReturn($qb);
        $qb->shouldReceive('eq')->andReturn($qb);
        $qb->shouldReceive('setParameter');
        $qb->shouldReceive('getQuery')->andReturn($query);
        $this->em->shouldReceive('createQueryBuilder')->andReturn($qb);
        $query->shouldReceive('getResult')->andReturn($result);

        $userId = null;
        $resourceId = 1;
        $this->assertNotNull($this->object->getAcl($userId, $resourceId));
        $this->assertType('Zend_Acl', $this->object->getAcl($userId, $resourceId));
        $this->assertTrue(
                $this->object->getAcl($userId, $resourceId)
                ->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name'));
        $this->assertFalse(
                $this->object->getAcl($userId, $resourceId)
                ->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name1'));

        $this->assertTrue(
                $this->object->getAcl($userId, $resource)
                ->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name'));
        $this->assertFalse(
                $this->object->getAcl($userId, $resource)
                ->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name1'));

    }

    public function testGetAclByUserAndResource()
    {
        $resource = m::mock('\\Zend_Acl_Resource_Interface');
        $resource->shouldReceive('getResourceId')->andReturn('test-resource-id');
        $resource->shouldReceive('getId')->andReturn(1);

        $permission = m::mock('Permission');
        $permission->shouldReceive('getResource')->andReturn($resource);
        $permission->shouldReceive('getName')->andReturn('test-permission-name');
        $permission->shouldReceive('getType')->andReturn(true);
        $permission1 = m::mock('Permission');
        $permission1->shouldReceive('getResource')->andReturn($resource);
        $permission1->shouldReceive('getName')->andReturn('test-permission-name1');
        $permission1->shouldReceive('getType')->andReturn(false);

        $permissions = array(
            $permission,
            $permission1
        );
        $role = m::mock('Role');
        $role->shouldReceive('getPermissions')->andReturn($permissions);
        $role->shouldReceive('getRoleId')->andReturn('test-role-id');
        $roles = array(
            $role,
            $role
        );
        $user = m::mock('User');
        $user->shouldReceive('getAllRoles')->andReturn($roles);
        $result = array(
            $user,
            $user
        );
        $query = m::mock('Query');
        $qb = m::mock('QueryBuilder');
        $qb->shouldReceive('select')->andReturn($qb);
        $qb->shouldReceive('from')->andReturn($qb);
        $qb->shouldReceive('leftJoin')->andReturn($qb);
        $qb->shouldReceive('where')->andReturn($qb);
        $qb->shouldReceive('andWhere')->andReturn($qb);
        $qb->shouldReceive('expr')->andReturn($qb);
        $qb->shouldReceive('eq')->andReturn($qb);
        $qb->shouldReceive('setParameter');
        $qb->shouldReceive('getQuery')->andReturn($query);
        $this->em->shouldReceive('createQueryBuilder')->andReturn($qb);
        $query->shouldReceive('getResult')->andReturn($result);

        $userId = 1;
        $resourceId = 1;
        $this->assertNotNull($this->object->getAcl($userId, $resourceId));
        $this->assertType('Zend_Acl', $this->object->getAcl($userId, $resourceId));
        $this->assertTrue(
                $this->object->getAcl($userId, $resourceId)
                ->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name'));
        $this->assertFalse(
                $this->object->getAcl($userId, $resourceId)
                ->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name1'));

        $this->assertTrue(
                $this->object->getAcl($userId, $resource)
                ->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name'));
        $this->assertFalse(
                $this->object->getAcl($userId, $resource)
                ->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name1'));

    }

    public function testGetGuestAcl()
    {
        $resource = m::mock('\\Zend_Acl_Resource_Interface');
        $resource->shouldReceive('getResourceId')->andReturn('test-resource-id');
        $resource->shouldReceive('getId')->andReturn(1);

        $permission = m::mock('Permission');
        $permission->shouldReceive('getResource')->andReturn($resource);
        $permission->shouldReceive('getName')->andReturn('test-permission-name');
        $permission->shouldReceive('getType')->andReturn(true);
        $permission1 = m::mock('Permission');
        $permission1->shouldReceive('getResource')->andReturn($resource);
        $permission1->shouldReceive('getName')->andReturn('test-permission-name1');
        $permission1->shouldReceive('getType')->andReturn(false);

        $permissions = array(
            $permission,
            $permission1
        );
        $role = m::mock('Role');
        $role->shouldReceive('getPermissions')->andReturn($permissions);
        $role->shouldReceive('getRoleId')->andReturn('test-role-id');
        $roles = array(
            $role,
            $role
        );
        
        $guestGroup = m::mock('Group');
        $guestGroup->shouldReceive('getAllRoles')->andReturn($roles);
        $result = array($guestGroup);
        $query = m::mock('Query');
        $query->shouldReceive('setParameter')->andReturn($query);
        $query->shouldReceive('getResult')->andReturn($result);
        $this->em->shouldReceive('createQuery')->andReturn($query);

        $this->assertNotNull($this->object->getAcl('guest'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShouldRaiseExceptionIfAclIncorrect()
    {
        $acl = new \stdClass();
        $this->object->setAcl($acl, $this->currentUser);
    }

    public function testBuildAclForUser()
    {
        $result = array();

        $user = m::mock('User');
        $query = m::mock('Query');
        $query->shouldReceive('getResult')->andReturn($result);
        $this->em->shouldReceive('createQuery')->andReturn($query);

        //$this->assertEquals($this->object->buildAcl($user));
    }
}