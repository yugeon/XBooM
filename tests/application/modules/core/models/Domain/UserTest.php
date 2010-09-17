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

namespace test\Core\Model\Domain;
use \Core\Model\Domain\User,
    \Mockery as m;

class UserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Application_Model_Domain_User
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new User;
    }

    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testGetSetUserName()
    {
        $testName = 'Vasya';
        $this->object->setName($testName);
        $this->assertEquals($testName, $this->object->getName());
    }

    public function testGetSetUserEmail()
    {
        $testEmail = 'Vasya@mail.com';
        $this->object->setEmail($testEmail);
        $this->assertEquals($testEmail, $this->object->getEmail());
    }

    public function testCanAssignToGroup()
    {
        $group = m::mock('Group');
        $this->assertEquals($this->object, $this->object->setGroup($group));
        $this->assertEquals($group, $this->object->getGroup());
    }

    public function testShouldImplementZendAclRoleInterface()
    {
        $this->assertType('Zend_Acl_Role_Interface', $this->object);
    }

    public function testDefaultUserShouldReturnGuestRole()
    {
        $this->markTestSkipped();
        $expected = array(
            'Guest',
        );
        $this->assertEquals($expected, $this->object->getRoleId());
    }

    public function  testCanAssignToPersonalRole()
    {
        $role = m::mock('Role');
        $role->shouldReceive('markPersonal');
        $this->object->setRole($role);
        $this->assertEquals($role, $this->object->getRole());
    }

    public function testGetRoleIdShouldReturnAllRolesAssignedToUser()
    {
        $groupRoles = array(
            m::mock('Role'),
            m::mock('Role'),
        );
        $userGroup = m::mock('Group');
        $userGroup->shouldReceive('getRoleId')->andReturn($groupRoles);
        $userRole = m::mock('Role');
        $userRole->shouldReceive('markPersonal');
        $this->object->setId(545);
        $this->object->setRole($userRole);
        $this->object->setGroup($userGroup);

        $expected = $groupRoles;
        $expected[] = $userRole;
        
        $this->assertSame($expected, $this->object->getRoleId());
    }

    public function testPersonalRoleShouldBeEspecially()
    {
        $userRole = m::mock('Role');
        $userRole->shouldReceive('markPersonal')->with(true)->once();
        $userRole->shouldReceive('isPersonal')->andReturn(true);
        $this->object->setRole($userRole);

        $this->assertTrue($this->object->getRole()->isPersonal());
    }

    public function testShouldImplementZendAclResourceInterface()
    {
        $this->assertType('Zend_Acl_Resource_Interface', $this->object);
    }

    public function testGetResourceId()
    {
        $resourceId = 326;
        $resource = m::mock('\\Core\\Model\\Domain\\Resource');
        $resource->shouldReceive('getId')->andReturn($resourceId);
        $this->object->setResource($resource);
        $this->assertEquals('User-' . $resourceId, $this->object->getResourceId());
    }

    /**
     * @expectedException \Xboom\Model\Exception
     */
    public function testShouldRaiseExceptionIfResourceNotAssign()
    {
        $this->object->getResourceId();
    }

    public function testGetPermissionsWithRolesAndResources()
    {
        $this->markTestSkipped();
        $group1RPR = array(
            'role' => 'Group-1',
            'permissions' => array(
                   array(
                       'name' => 'priv-1',
                       'type' => true,
                       'res' =>  'priv-1',
                    ),
            )
        );
        $group2RPR = array(
            'role' => 'Group-2',
            'permissions' => array(
                   array(
                       'name' => 'priv-2',
                       'type' => true,
                       'res' =>  '2',
                    ),
            )
        );

        $group1 = m::mock('Group');
        $group2 = m::mock('Group');
        $group1->shouldReceive('getRolesPermissionsAndResources')->andReturn($group1RPR);
        $group2->shouldReceive('getRolesPermissionsAndResources')->andReturn($group2RPR);
        $resource = m::mock('Resource');
        $resource->shouldReceive('getId')->andReturn(3);
        $permission = m::mock('Permission');
        $permission->shouldReceive('getName')->andReturn('priv-3');
        $permission->shouldReceive('getType')->andReturn(1);
        $permission->shouldReceive('getResource')->andReturn($resource);

        $this->object->setId('3');
        $this->object->assignToGroup($group1);
        $this->object->assignToGroup($group2);
        $this->object->assignToPermission($permission);

        $expected = array(
            array(
                'role' => 'Group-1',
                'permissions' => array(
                    array(
                       'name' => 'priv-1',
                       'type' => true,
                       'res' =>  'priv-1',
                    ),
                ),
            ),
            array(
               'role' => 'Group-2',
               'permissions' => array(
                    array(
                       'name' => 'priv-2',
                       'type' => true,
                       'res' =>  '2',
                    ),
                ),
            ),
            array(
                'role' => 'User-3',
                'permissions' => array(
                    array(
                       'name' => 'priv-3',
                       'type' => true,
                       'res' =>  '3',
                    ),
                 ),
            ),
        );

        $this->assertEquals($expected, $this->object->getRolesPermissionsAndResources());


    }
}