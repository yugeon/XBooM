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
 * Description of AclTest
 *
 * @author yugeon
 */
namespace test\Xboom\Acl;
use \Xboom\Acl\Acl,
    \Mockery as m;

class AclTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var \Core\Model\Domain\Acl;
     */
    protected $object;

    public function setUp()
    {
        parent::setUp();
        $this->object = new Acl;
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }
    
    public function testCanAddRole()
    {
        $this->assertEquals($this->object, $this->object->addRole('TestRole'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRoleInheritanceDisallow()
    {
        $this->object->addRole('TestRole', 'ParentRole');
    }

    public function testCanAddResource()
    {
        $this->assertEquals($this->object, $this->object->addResource('TestRole'));
    }

    public function testCanAddSubResource()
    {
        $parent = 'Parent';
        $this->object->addResource($parent);
        $this->assertEquals($this->object, $this->object->addResource('Child', $parent));
    }

    public function testDefaultAllDenied()
    {
        $this->assertFalse($this->object->isAllowed(null, null, null));
    }

    public function testCanAddRolesAsArray()
    {
        $roles = array(
            'Group-1',
            'Group-3',
            'User-2'
        );

        $this->object->addRole($roles);
        $this->assertEquals($roles, $this->object->getRoles());
    }

    public function testCanCheckRole()
    {
        $role = 'User-232';
        $resource = new \Zend_Acl_Resource('resource');
        $this->object->addRole($role);
        //$this->object->add($resource);
        $this->object->allow($role);
        $this->assertTrue($this->object->isAllowed($role));
    }

    public function testCheckRoleAndResource()
    {
        $role = 'User-232';
        $resource = new \Zend_Acl_Resource('resource');
        $this->object->addRole($role);
        $this->object->add($resource);
        $this->object->allow($role);
        $this->assertTrue($this->object->isAllowed($role, $resource));
    }

    public function testShouldReturnFalseIfRoleDosnotExists()
    {
        $role = 'User-232';
        $this->assertFalse($this->object->isAllowed($role));
    }

    public function testShouldReturnFalseIfResourceDosnotExists()
    {
        $this->object->addRole('role-232');
        $resource = new \Zend_Acl_Resource('non-exists-resource-id');
        $this->assertFalse($this->object->isAllowed('role-232', $resource));
    }

    public function testCanCheckArrayRoles()
    {
        $roles = array(
            'Role-1',
            'Role-2',
            'Role-3'
        );
        $this->object->addRole($roles);
        $this->object->allow($roles[2]);

        $this->assertTrue($this->object->isAllowed($roles));
    }

    public function testCheckNestedResources()
    {
         $parentResource = 'parent';
         $childResource = 'child';
         $this->object->addResource($parentResource);
         $this->object->addResource($childResource, $parentResource);

         $role = 'User-232';
         $this->object->addRole($role);
         $this->object->allow($role, $parentResource);

         $this->assertTrue($this->object->isAllowed($role, $childResource));
    }

    public function testRememberVerifiableUser()
    {
        $user = m::mock('Zend_Acl_Role_Interface');
        $roleId = 'test-role-id';
        $user->shouldReceive('getRoleId')->andReturn($roleId);
        $this->object->isAllowed($user, null, null);
        $this->assertEquals($user, $this->object->getRememberedUser());
    }
}
