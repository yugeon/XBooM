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
 * Description of AddMenuValidatorTest
 *
 * @author yugeon
 */

namespace test\App\Admin\Model\Domain\Validator;

use \App\Admin\Model\Domain\Validator\AddMenuValidator,
 \Mockery as m;

class AddMenuValidatorTest extends \PHPUnit_Framework_TestCase
{

    protected $object;

    public function setUp()
    {
        $this->object = new AddMenuValidator;
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

    public function testInit()
    {
        $this->assertArrayHasKey('name', $this->object->getPropertiesForValidation());
        $this->assertArrayHasKey('description', $this->object->getPropertiesForValidation());
    }

}
