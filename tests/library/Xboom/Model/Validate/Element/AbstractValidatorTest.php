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
 * Description of AbstractValidatorTest
 *
 * @author yugeon
 */
require_once 'PHPUnit/Framework.php';
use Xboom\Model\Validate\Element\ValidatorInterface;
use Xboom\Model\Validate\Element\AbstractValidator;

class TestElementValidator extends AbstractValidator
{

}

class AbstractValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * 
     * @var TestElementValidator
     */
    protected $object;

    public function setUp()
    {
        parent::setUp();
        $this->object = new TestElementValidator();
    }

    public function testCanAddValidator()
    {
        $validator = new Zend_Validate_Alnum();
        $this->assertEquals($this->object, $this->object->addValidator($validator));
        $this->assertContains($validator, $this->object->getValidators());
    }
    public function testCanAddValidatorsByChain()
    {
        $validator1 = new Zend_Validate_Alnum();
        $validator2 = new Zend_Validate_Alpha();
        $this->object->addValidator($validator1)
                     ->addValidator($validator2);
        $this->assertContains($validator1, $this->object->getValidators());
        $this->assertContains($validator2, $this->object->getValidators());
    }
    /**
     * @expectedException \Exception
     */
    public function testValidatorMustImplementZendValidateInterface()
    {
        $validator = new stdClass();
        $this->object->addValidator($validator);
    }
    public function testCanValidateValue()
    {
        $value = 'validValue';
        $validator = new Zend_Validate_Alnum();
        $this->object->addValidator($validator);
        $this->assertTrue($validator->isValid($value));
        $this->assertTrue($this->object->isValid($value));
    }
    public function testMustReturnFalseIfValueIsInvalid()
    {
        $value = '#@$! invalidValue %$$@^#';
        $validator = new Zend_Validate_Alnum();
        $this->object->addValidator($validator);
        $this->assertFalse($validator->isValid($value));
        $this->assertFalse($this->object->isValid($value));
    }
    public function testMethodGetMessageShouldReturnEmptyArrayIfDataValid()
    {
        $value = 'validValue';
        $validator = new Zend_Validate_Alnum();
        $this->object->addValidator($validator);
        $this->assertEquals(0, count($this->object->getMessages()));
    }
    public function testMethodGetMessagesShouldReturnArrayOfErrorsIfDataInvalid()
    {
        $value = '#@$validValue #@@';
        $validator = new Zend_Validate_Alnum();
        $this->object->addValidator($validator);
        $this->object->isValid($value);
        $this->assertTrue(count($this->object->getMessages()) > 0);
    }
    public function testCanBreakValidationChainIfValueIsInvalid()
    {
        $value = '#@$! invalidValue %$$@^#';
        $validator1 = new Zend_Validate_Alnum();
        $validator2 = new Zend_Validate_Digits();
        $this->object->addValidator($validator1, true)
                     ->addValidator($validator2);
        $this->assertFalse($validator1->isValid($value));
        $this->assertFalse($validator2->isValid($value));
        $this->assertFalse($this->object->isValid($value));
        $this->assertEquals(1, count($this->object->getMessages()));
    }
}
