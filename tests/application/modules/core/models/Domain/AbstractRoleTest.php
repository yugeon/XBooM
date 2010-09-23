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
 * Description of AbstractRoleTest
 *
 * @author yugeon
 */
namespace test\Core\Model\Domain;

use \Core\Model\Domain\AbstractRole,
    \Mockery as m;

class Subject extends AbstractRole
{

    public function  getRoleId()
    {
        return;
    }
}

class AbstractRoleTest extends \PHPUnit_Framework_TestCase
{

    protected $object;

    public function setUp()
    {
        $this->object = new Subject;
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testAssingToPermissions()
    {
        $permission1 = m::mock('Permission');
        $permission2 = m::mock('Permission');
        $permission3 = m::mock('Permission');

        $this->object->assignToPermission($permission1);
        $this->object->assignToPermission($permission2);
        $this->object->assignToPermission($permission3);

        $this->assertEquals( 3, count($this->object->getPermissions()) );
    }

}
