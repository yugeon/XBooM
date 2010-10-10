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
namespace test\App\Core\Model\Service;

use App\Core\Model\Service\AclService,
    \Mockery as m;

class AclServiceTest extends \PHPUnit_Framework_TestCase
{

    protected $object;
    protected $em;
    protected $role;
    protected $resource;
    protected $permission;
    protected $permission1;

    public function setUp()
    {
        $this->em = m::mock('\\Doctrine\\ORM\\EntityManager');
        $this->em->shouldReceive('persist');
        $this->em->shouldReceive('flush');
        $this->em->shouldReceive('getRepository')->andReturn($this->em);

        $this->object = new AclService($this->em);

        $this->resource = m::mock('Zend_Acl_Resource_Interface');
        $this->resource->shouldReceive('getId')->andReturn(2);
        $this->resource->shouldReceive('getLevel')->andReturn(0);
        $this->resource->shouldReceive('getResourceId')->andReturn('test-resource-id');
        $this->resource->shouldReceive('getParent')->andReturn(null);

        $this->permission = m::mock('Permission');
        $this->permission->shouldReceive('getResource')->andReturn($this->resource);
        $this->permission->shouldReceive('isOwnerRestriction')->andReturn(false);
        $this->permission->shouldReceive('getId')->andReturn(2);
        $this->permission->shouldReceive('getName')->andReturn('test-permission-name');
        $this->permission->shouldReceive('getType')->andReturn(true);

        $this->permission1 = m::mock('\\Xboom\\Model\\Domain\\Acl\\Permission');
        $this->permission1->shouldReceive('getResource')->andReturn($this->resource);
        $this->permission1->shouldReceive('isOwnerRestriction')->andReturn(false);
        $this->permission1->shouldReceive('getName')->andReturn('test-permission-name1');
        $this->permission1->shouldReceive('getType')->andReturn(false);
        $this->permission1->shouldReceive('getRoles')->andReturn(array());

        $permissions = array(
            $this->permission,
            $this->permission1
        );

        $this->resource->shouldReceive('getPermissions')->andReturn($permissions);

        $this->role = m::mock('Zend_Acl_Role_Interface');
        $this->role->shouldReceive('getPermissions')->andReturn($permissions);
        $this->role->shouldReceive('getRoleId')->andReturn('test-role-id');

        $roles = array(
            $this->role,
            $this->role
        );

        $this->permission->shouldReceive('getRoles')->andReturn($roles);

        $result = array(
            $this->resource,
            $this->resource
        );

        $query = m::mock('Query');
        $query->shouldReceive('setParameter')->andReturn($query);
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
        $this->em->shouldReceive('createQuery')->andReturn($query);
        $this->em->shouldReceive('find')->andReturn($this->resource);
        $this->em->shouldReceive('findOneByName')->andReturn($this->resource);
        $query->shouldReceive('getResult')->andReturn($result);
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
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

    public function testGetAclByRole()
    {
        $roleId = 1;
        $acl = $this->object->getAcl($roleId);
        $this->assertNotNull($acl);
        $this->assertType('Zend_Acl', $acl);
        $this->assertTrue(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name'));
        $this->assertFalse(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name1'));

        $acl = $this->object->getAcl($this->role);
        $this->assertTrue(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name'));
        $this->assertFalse(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name1'));
    }

    public function testGetAclByRoles()
    {
        $role2 = m::mock('Zend_Acl_Role_Interface');
        $role2->shouldReceive('getPermissions')->andReturn($this->permission1);
        $role2->shouldReceive('getRoleId')->andReturn('test-role-id2');

        $roles = array(
            $this->role,
            $role2
        );

        $acl = $this->object->getAcl($roles);
        $this->assertNotNull($acl);
        $this->assertType('Zend_Acl', $acl);
        $this->assertTrue(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name'));
        $this->assertFalse(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name1'));
    }

    public function testGetAclByResource()
    {
        $roleId = null;
        $resourceId = $this->resource->getResourceId();
        $acl = $this->object->getAcl($roleId, $resourceId);
        $this->assertNotNull($acl);
        $this->assertType('Zend_Acl', $acl);
        $this->assertTrue(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name'));
        $this->assertFalse(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name1'));

        $acl = $this->object->getAcl($roleId, $this->resource);
        $this->assertTrue(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name'));
        $this->assertFalse(
                $acl->isAllowed('test-role-id', 'test-resource-id', 'test-permission-name1'));

    }

    public function testGetAclByUserAndResource()
    {
        $userId = 1;
        $resourceId = $this->resource->getResourceId();
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
        $resourceId = $this->resource->getResourceId();
        $permissionId = 'test-permission-name';
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

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShouldRaiseExceptionIfAclIncorrect()
    {
        $acl = new \stdClass();
        $this->object->setAcl($acl, 'acl_id');
    }

    public function testGetSetAssertions()
    {
        $assertName = 'Owner';
        $assertion = m::mock('Zend_Acl_Assert_Interface');
        $this->object->setAssertion($assertName, $assertion);
        $this->assertEquals($assertion, $this->object->getAssertion($assertName));
    }

}