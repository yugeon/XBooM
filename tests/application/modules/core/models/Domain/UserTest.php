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

    public function testCanAssignToGroup()
    {
        $group = m::mock('Group');
        $this->assertEquals($this->object, $this->object->assignToGroup($group));
        $this->assertContains($group, $this->object->getGroups());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGroupMustBeObject()
    {
        $this->object->assignToGroup(null);
    }

    public function testUserCanBelongToSeveralGroups()
    {
        $group1 = m::mock('Group');
        $group2 = m::mock('Group');
        $this->object->assignToGroup($group1)
                     ->assignToGroup($group2);
        $this->assertTrue(2 == count($this->object->getGroups()) );
    }

    public function testIfUserConsistInGroupNoDouble()
    {
        $group1 = m::mock('Group');
        $group2 = m::mock('Group');
        $this->object->assignToGroup($group1)
                     ->assignToGroup($group2)
                     ->assignToGroup($group1);
        $this->assertTrue(2 == count($this->object->getGroups()) );
    }

    public function testDefaultUserConsistsInGuestGroup()
    {
        $this->markTestSkipped();
        $guestGroup = m::mock('Group');
        $guestGroup->shouldReceive('getName')->andReturn('Guest');

        $this->assertEquals('Guest', $this->object->getGroups()->getName());
    }

    public function testSetGetRole()
    {
        $roleId = 1;
        $role = m::mock('Role');
        $role->shouldReceive('getRoleId')->andReturn($roleId);
        $this->object->setRole($role);
        $this->assertEquals($role, $this->object->getRole());
    }

    public function testGetAllRolesAssignedToUser()
    {
        $role1 = m::mock('Role');
        $role2 = m::mock('Role');
        $role3 = m::mock('Role');
        $role1->shouldReceive('getRoleId')->andReturn('1');
        $role2->shouldReceive('getRoleId')->andReturn('2');
        $role3->shouldReceive('getRoleId')->andReturn('3');

        $group1 = m::mock('Group');
        $group2 = m::mock('Group');
        $group1->shouldReceive('getRole')->andReturn($role1);
        $group2->shouldReceive('getRole')->andReturn($role2);
        $this->object->setRole($role3);
        $this->object->assignToGroup($group1);
        $this->object->assignToGroup($group2);

        $expected = array(
            '1',
            '2',
            '3',
        );
        $this->assertSame($expected, $this->object->getRoles());

    }
}