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
 * Test case for ClassName
 *
 * @author yugeon
 */
namespace test\App\Core\Model\Domain\Acl;
use \App\Core\Model\Domain\Acl\Resource,
    \Mockery as m;

class ResourceTest extends \PHPUnit_Framework_TestCase
{
    protected $object;
    protected $parentResource;

    public function setUp()
    {
        $this->object = new Resource;

        $this->parentResource = m::mock('Resource');
        $this->parentResource->shouldReceive('getLevel')->andReturn(1);
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testCanCreateResource()
    {
        $this->assertNotNull($this->object);
    }

    public function testGetResourceId()
    {
        $this->object->setName('News');
        $this->assertEquals('News', $this->object->getResourceId());
    }

    public function testGetParent()
    {
        $this->object->setParent($this->parentResource);
        $this->assertEquals($this->parentResource, $this->object->getParent());
    }

    public function testGetSetLevelOfNesting()
    {
        $this->object->setLevel(2);
        $this->assertEquals(2, $this->object->getLevel());
    }

    public function testCalculationOfNestingLevel()
    {
        $this->object->setParent($this->parentResource);
        $this->assertEquals(2, $this->object->getLevel());
    }

    public function testGetAllParents()
    {
        $resourceNews = m::mock('Resource');
        $resourceConfirmNews = m::mock('Resource');

        $resourceNews->shouldReceive('getParent')->andReturn(null);
        $resourceConfirmNews->shouldReceive('getParent')->andReturn($resourceNews);

        $resourceNews->shouldReceive('getAllParents')->andReturn(array());
        $resourceConfirmNews->shouldReceive('getAllParents')->andReturn(array($resourceNews));
        $resourceConfirmNews->shouldReceive('getLevel')->andReturn(2);
        
        $this->object->setParent($resourceConfirmNews);

        $expected = array(
            $resourceNews,
            $resourceConfirmNews,
        );

        $this->assertEquals($expected, $this->object->getAllParents());
    }

    public function testGetSetOwner()
    {
        $owner = m::mock('User');
        $this->object->setOwner($owner);
        $this->assertEquals($owner, $this->object->getOwner());
    }
}