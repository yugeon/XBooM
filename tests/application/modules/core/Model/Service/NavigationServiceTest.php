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
 * Description of NavigationServiceTest
 *
 * @author yugeon
 */

namespace test\App\Core\Model\Service;

use \App\Core\Model\Service\NavigationService,
 \Mockery as m;

class NavigationServiceTest extends \PHPUnit_Framework_TestCase
{

    protected $object;
    /**
     *
     * @var EntityManager
     */
    protected $em;

    public function setUp()
    {
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
        $qb->shouldReceive('orderBy')->andReturn($qb);
        $qb->shouldReceive('setParameter')->andReturn($qb);
        $qb->shouldReceive('getQuery')->andReturn($query);

        $result = array();
        
        $this->em = m::mock('\\Doctrine\\ORM\\EntityManager');
        $this->em->shouldReceive('createQueryBuilder')->andReturn($qb);
        $this->em->shouldReceive('createQuery')->andReturn($query);
        $query->shouldReceive('getResult')->andReturn($result);

        $this->object = new NavigationService($this->em);
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testCanCreateTestObject()
    {
        $this->assertNotNull($this->object);
    }

    public function testGetNavigation()
    {
        $this->assertType('Zend_Navigation_Container', $this->object->getNavigation());
    }

    public function _testBuildNavigation()
    {
        $this->object->buildNavigationByName($name);
    }
}