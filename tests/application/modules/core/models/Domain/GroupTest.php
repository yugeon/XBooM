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
 * Test case for Group
 *
 * @author yugeon
 */
namespace test\Core\Model\Domain;
use \Core\Model\Domain\Group,
    \Mockery as m;

class GroupTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var \Core\Model\Domain\Group
     */
    protected $object;

    public function setUp()
    {
        parent::setUp();
        $this->object = new Group;
    }

    public function tearDown()
    {
        m::close();
    }

    public function testShouldImplementZendAclRoleInterface()
    {
        $this->assertType('Zend_Acl_Role_Interface', $this->object);
    }

    public function testGetAllRolesShouldReturnArrayOfRoles()
    {
        $this->object->setId(1);
        $role1 = m::mock('Role');
        $role2 = m::mock('Role');
        $role3 = m::mock('Role');
        $role1->shouldReceive('getRoleId')->andReturn('1');
        $role2->shouldReceive('getRoleId')->andReturn('2');
        $role3->shouldReceive('getRoleId')->andReturn('3');

        $this->object->assignToRole($role1);
        $this->object->assignToRole($role2);
        $this->object->assignToRole($role3);

        $expectedRoles = array(
            $role1,
            $role2,
            $role3,
        );

        $this->assertEquals($expectedRoles, $this->object->getAllRoles());
    }

    public function testGetRoleIdShouldReturnNullIfGroupNotPersisted()
    {
        $this->assertNull($this->object->getRoleId());
    }
}
