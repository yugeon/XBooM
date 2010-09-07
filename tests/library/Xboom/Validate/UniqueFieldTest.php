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
 * Test case for UniqueField validator.
 *
 * @author yugeon
 */
namespace test\Xboom\Validate;
use \Xboom\Validate\UniqueField,
    \Mockery as m;

class UniqueFieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var \Xboom\Validate\UniqueField
     */
    protected $object;

    /**
     *
     * @var \Doctrine\ORM\Query
     */
    protected $query;

    public function setUp()
    {
        parent::setUp();

        $em = m::mock('\\Doctrine\\ORM\\EntityManager');

        $this->query = m::mock('\\Doctrine\\ORM\\Query');

        $queryBuilder = m::mock('\\Doctrine\\ORM\\QueryBuilder');
        $queryBuilder->shouldReceive('select')->andReturn($queryBuilder);
        $queryBuilder->shouldReceive('from')->andReturn($queryBuilder);
        $queryBuilder->shouldReceive('where')->andReturn($queryBuilder);
        $queryBuilder->shouldReceive('setParameter')->andReturn($queryBuilder);
        $queryBuilder->shouldReceive('getQuery')->andReturn($this->query);
        $queryBuilder->shouldReceive('expr')->andReturn($queryBuilder);
        $queryBuilder->shouldReceive('eq')->andReturn($queryBuilder);

        $em->shouldReceive('createQueryBuilder')->andReturn($queryBuilder);

        $testEntityName = 'User';
        $testUniqueField = 'login';

        $options = array(
            'em'     => $em,
            'entity' => $testEntityName,
            'field'  => $testUniqueField
        );
        $this->object = new UniqueField($options);
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }
    
    public function testCanCreateUniqueFieldValidator()
    {
        $this->assertNotNull($this->object);
        $this->assertType('\\Zend_Validate_Interface', $this->object);
    }

    public function testIfFieldValueIsUniqueMustReturnTrue()
    {
        $uniqueFieldValue = 'Unique Value';
        $this->query->shouldReceive('execute')->andReturn(array());

        $this->assertTrue($this->object->isValid($uniqueFieldValue));
    }

    public function testIfFieldValueIsNotUniqueMustReturnFalse()
    {
        $notUniqueFieldValue = 'Not Unique Value';
        $this->query->shouldReceive('execute')->andReturn(array($notUniqueFieldValue));

        $this->assertFalse($this->object->isValid($notUniqueFieldValue));
    }
}
