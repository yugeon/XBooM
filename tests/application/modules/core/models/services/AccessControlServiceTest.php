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
    protected $user;
    protected $resource;
    protected $permission;

    public function setUp()
    {
        $this->em = m::mock('\\Doctrine\\ORM\\EntityManager');
        $this->em->shouldReceive('persist');
        $this->em->shouldReceive('flush');

        $this->object = new AccessControlService($this->em);

        $this->user = m::mock('User');
        $this->user->shouldReceive('getId')->andReturn(232);

        $this->resource = m::mock('\\Zend_Acl_Resource_Interface');
        $this->resource->shouldReceive('getId')->andReturn(2);
        $this->resource->shouldReceive('getLevel')->andReturn(0);
        $this->resource->shouldReceive('getResourceId')->andReturn('test-resource-id');
        $this->resource->shouldReceive('getParent')->andReturn(null);

        $this->permission = m::mock('Permission');
        $this->permission->shouldReceive('getResource')->andReturn($this->resource);
        $this->permission->shouldReceive('getId')->andReturn(2);
        $this->permission->shouldReceive('getName')->andReturn('test-permission-name');
        $this->permission->shouldReceive('getType')->andReturn(true);

        $permission1 = m::mock('Permission');
        $permission1->shouldReceive('getResource')->andReturn($this->resource);
        $permission1->shouldReceive('getName')->andReturn('test-permission-name1');
        $permission1->shouldReceive('getType')->andReturn(false);

        $permissions = array(
            $this->permission,
            $permission1
        );

        $role = m::mock('Role');
        $role->shouldReceive('getPermissions')->andReturn($permissions);
        $role->shouldReceive('getRoleId')->andReturn('test-role-id');

        $roles = array(
            $role,
            $role
        );
        $this->user->shouldReceive('getAllRoles')->andReturn($roles);

        $result = array(
            $this->user,
            $this->user
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
        $qb->shouldReceive('in')->andReturn($qb);
        $qb->shouldReceive('setParameter');
        $qb->shouldReceive('getQuery')->andReturn($query);

        $this->em->shouldReceive('createQueryBuilder')->andReturn($qb);
        $this->em->shouldReceive('find')->andReturn($this->resource);
        $query->shouldReceive('getResult')->andReturn($result);
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testSetAcl()
    {
        $acl = m::mock('Zend_Acl');

        $this->object->setAcl($acl, $this->object->getAclId('guest'));
        $this->assertEquals($acl, $this->object->getAcl('guest'));
    }

    public function testGetFullAcl()
    {
        $acl = $this->object->getAcl();
        $this->assertNotNull($acl);
        $this->assertType('Zend_Acl', $acl);
        $this->assertTrue(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name'));
        $this->assertFalse(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name1'));
    }

    public function testGetAclByUser()
    {
        $userId = 1;
        $acl = $this->object->getAcl($userId);
        $this->assertNotNull($acl);
        $this->assertType('Zend_Acl', $acl);
        $this->assertTrue(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name'));
        $this->assertFalse(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name1'));

        $acl = $this->object->getAcl($this->user);
        $this->assertTrue(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name'));
        $this->assertFalse(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name1'));
    }

    public function testGetAclByResource()
    {
        $userId = null;
        $resourceId = 1;
        $acl = $this->object->getAcl($userId, $resourceId);
        $this->assertNotNull($acl);
        $this->assertType('Zend_Acl', $acl);
        $this->assertTrue(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name'));
        $this->assertFalse(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name1'));

        $acl = $this->object->getAcl($userId, $this->resource);
        $this->assertTrue(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name'));
        $this->assertFalse(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name1'));

    }

    public function testGetAclByUserAndResource()
    {
        $userId = 1;
        $resourceId = 1;
        $acl = $this->object->getAcl($userId, $resourceId);
        $this->assertNotNull($acl);
        $this->assertType('Zend_Acl', $acl);
        $this->assertTrue(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name'));
        $this->assertFalse(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name1'));

        $acl = $this->object->getAcl($userId, $this->resource);
        $this->assertTrue(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name'));
        $this->assertFalse(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name1'));

    }

    public function testGetAclByUserResourceAndPermission()
    {
        $userId = 1;
        $resourceId = 1;
        $permissionId = 1;
        $acl = $this->object->getAcl($userId, $resourceId, $permissionId);
        $this->assertNotNull($acl);
        $this->assertType('Zend_Acl', $acl);
        $this->assertTrue(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name'));
        $this->assertFalse(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name1'));

        $acl = $this->object->getAcl($userId, $this->resource, $this->permission);
        $this->assertTrue(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name'));
        $this->assertFalse(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name1'));

    }

    public function testGetGuestAcl()
    {
        $resource = m::mock('\\Zend_Acl_Resource_Interface');
        $resource->shouldReceive('getResourceId')->andReturn('test-resource-id');
        $resource->shouldReceive('getParent')->andReturn(null);
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
        $this->object->setAcl($acl, $this->user);
    }

}