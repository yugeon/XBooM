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
 * Description of RoleTest
 *
 * @author yugeon
 */
namespace test\App\Core\Model\Domain\Acl;
use \App\Core\Model\Domain\Acl\Role,
    \Mockery as m;

class RoleTest extends \PHPUnit_Framework_TestCase
{
    protected $object;

    public function setUp()
    {
        $this->object = new Role;
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }
    
    public function testCanCreateRole()
    {
        $this->assertNotNull($this->object);
    }

    public function testCanMarkPersonal()
    {
        $this->object->markPersonal(true);
        $this->assertTrue($this->object->isPersonal());
    }

    public function testGetRoleID()
    {
        $this->object->setId(323);
        $this->assertEquals('323', $this->object->getRoleId());
    }

    public function testAssingToPermissions()
    {
        $permission1 = m::mock('Permission');
        $permission1->shouldReceive('assignToRole');
        $permission2 = m::mock('Permission');
        $permission2->shouldReceive('assignToRole');
        $permission3 = m::mock('Permission');
        $permission3->shouldReceive('assignToRole');

        $this->object->assignToPermission($permission1);
        $this->object->assignToPermission($permission2);
        $this->object->assignToPermission($permission3);

        $this->assertEquals( 3, count($this->object->getPermissions()) );
    }

    public function testWithoutDoublingOfPermissions()
    {
        $permission1 = m::mock('Permission');
        $permission1->shouldReceive('assignToRole');
        $permission2 = m::mock('Permission');
        $permission2->shouldReceive('assignToRole');

        $this->object->assignToPermission($permission1);
        $this->object->assignToPermission($permission2);
        $this->object->assignToPermission($permission1);

        $this->assertEquals( 2, count($this->object->getPermissions()) );
    }
}
