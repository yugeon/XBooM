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

        $this->object->setAcl($acl, $this->currentUser);
        $this->assertEquals($acl, $this->object->getAcl($this->currentUser));
    }

    public function _testGetAcl()
    {
        $result = array();
        $user = m::mock('User');
        $query = m::mock('Query');
        $query->shouldReceive('getResult')->andReturn($result);
        $query->shouldReceive('setParameter');
        $query->shouldReceive('getSingleResult');
        $this->em->shouldReceive('createQuery')->andReturn($query);
        
        $this->assertNotNull($this->object->getAcl($this->currentUser));
        $this->assertType('Zend_Acl', $this->object->getAcl($this->currentUser));
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