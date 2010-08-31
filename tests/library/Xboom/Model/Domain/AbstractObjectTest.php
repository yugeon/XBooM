<?php

require_once 'PHPUnit/Framework.php';
use Xboom\Model\Validate\ValidatorInterface;
use \Mockery as m;

class AbstractObject extends Xboom\Model\Domain\AbstractObject
{
    protected $testPropertyTrue = true;
    protected $testPropertyFalse = true;
    protected $testProperty;
    protected $_testPropertyTrue = true;
    protected $testProtectedProperty;
    protected $_testProtectedProperty;

    protected $login;
    protected $password;

    public function getTestPropertyFalse()
    {
        return false;
    }
    public function setTestProperty($value)
    {
        $this->testProperty = 'Set by mutator';
    }
}

class Xboom_Model_Domain_AbstractObjectTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Xboom_Model_Domain_AbstractObject
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new AbstractObject;
    }

    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testCanGetProtectedProperty()
    {
        $this->assertTrue( $this->object->testPropertyTrue );
    }
    public function testCannotGetProtectedPropertyWithUnderscoreSuffix()
    {
        try
        {
            $this->assertTrue( $this->object->_testPropertyTrue );
        }
        catch (InvalidArgumentException $exc)
        {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    public function testCannotGetNotExistenProperty()
    {
        try
        {
            $this->assertTrue( $this->object->notExistenProperty );
        }
        catch (InvalidArgumentException $exc)
        {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    public function testCanGetProtectedPropertyByAccessorMethod()
    {
        $this->assertFalse( $this->object->testPropertyFalse );
    }
    public function testCanSetProtectedProperty()
    {
        $testValue1 = 'New value';
        $this->object->testProtectedProperty = $testValue1;
        $this->assertEquals($testValue1, $this->object->testProtectedProperty);

        $testValue2 = 'Yet, another new value';
        $this->object->testProtectedProperty = $testValue2;
        $this->assertEquals($testValue2, $this->object->testProtectedProperty);
    }
    public function testCannotSetProtectedPropertyWithUnderscoreSuffix()
    {
        $testValue = 'test value';
        try
        {
            $this->object->_testProtectedProperty = $testValue;
        }
        catch (InvalidArgumentException $exc)
        {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    public function testCannotSetNotExistetProperty()
    {
        $testValue = 'test value';
        try
        {
            $this->object->testNotExistedProperty = $testValue;
        }
        catch (InvalidArgumentException $exc)
        {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    public function testCanSetProtectedPropertyByMutator()
    {
        $testValue = 'Test value';
        $this->object->testProperty = $testValue;
        $this->assertEquals('Set by mutator', $this->object->testProperty);
    }
    public function testCanGetProtectedPropertyByGetMethod()
    {
        $this->assertTrue( $this->object->getTestPropertyTrue() );
    }
    public function testCanSetProtectedPropertyByGetMethod()
    {
        $testValue1 = 'New value';
        $this->object->setTestProtectedProperty($testValue1);
        $this->assertEquals($testValue1, $this->object->getTestProtectedProperty());
    }
    public function testCannotGetProtectedPropertyWithUnderscoreSuffixByGetMethod()
    {
        try
        {
            $this->object->get_testPropertyTrue();
        }
        catch (BadMethodCallException $exc)
        {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    public function testCannontSetProtectedPropertyWithUnderscorePrefixBySetMethod()
    {
        $testValue = 'test value';
        try
        {
            $this->object->set_testProtectedProperty($testValue);
        }
        catch (BadMethodCallException $exc)
        {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
    public function testGetAllPropertiesAsArray()
    {
        $expected = array(
            "testPropertyTrue" => true,
            "testPropertyFalse" => true,
            "testProperty" => null,
            "testProtectedProperty"=> null,
            'login' => null,
            'password' => null,
        );
        $this->assertEquals($expected, $this->object->toArray());
    }

    // ------------------------------
    //  Implements ValidatorInterface
    // ------------------------------
    public function testCanSetValidator()
    {
        $validator = m::mock('Xboom\\Model\\Validate\\ValidatorInterface');
        $this->assertEquals($this->object, $this->object->setValidator($validator));
    }
    public function testMustReturnTrueIfDataIsValid()
    {
        $validData = array(
            'login'     => 'validLogin',
            'password'   => 'validPassword'
        );
        $validator = m::mock('Xboom\\Model\\Validate\\ValidatorInterface');
        $validator->shouldReceive('isValid')->once()->andReturn(true);
        $this->object = new AbstractObject($validData);
        $this->object->setValidator($validator);
        $this->assertTrue($this->object->isValid());

    }
    public function testMustReturnFalseIfDataIsInvalid()
    {
        $invalidData = array(
            'login'     => 'invalidLogin',
            'password'   => 'validPassword'
        );
        $validator = m::mock('Xboom\\Model\\Validate\\ValidatorInterface');
        $validator->shouldReceive('isValid')->once()->andReturn(false);
        $this->object = new AbstractObject($invalidData);
        $this->object->setValidator($validator);
        $this->assertFalse($this->object->isValid());

    }
    public function testCanValidateExternalArray()
    {
        $validData = array(
            'login' => 'validLogin',
            'password'   => 'validPassword'
        );
        $invalidData = array(
            'login' => 'invalidLogin',
            'password'   => 'validPassword'
        );
        $validator = m::mock('Xboom\\Model\\Validate\\ValidatorInterface');
        $validator->shouldReceive('isValid')->with($validData)->once()->andReturn(true);
        $validator->shouldReceive('isValid')->with($invalidData)->once()->andReturn(false);
        $this->object->setValidator($validator);

        $this->assertTrue($this->object->isValid($validData));
        $this->assertFalse($this->object->isValid($invalidData));
    }
    public function testDomainObjectMustBeValidIfValidatorNotSet()
    {
        $this->object->setValidator(null);
        $this->assertTrue($this->object->isValid());
    }
    public function testMethodGetMessagesShouldReturnEmptyArrayIfValidatorNotSet()
    {
        $this->object->setValidator(null);
        $this->assertEquals(array(), $this->object->getMessages());
    }
    public function testMethodGetMessagesShouldReturnEmptyArrayIfObjectValid()
    {
        $validData = array(
            'login'     => 'validLogin',
            'password'   => 'validPassword'
        );
        $validator = m::mock('Xboom\\Model\\Validate\\ValidatorInterface');
        $validator->shouldReceive('isValid')->once()->andReturn(true);
        $validator->shouldReceive('getMessages')->once()->andReturn(array());
        $this->object = new AbstractObject($validData);
        $this->object->setValidator($validator);
        $this->assertTrue($this->object->isValid());
        $this->assertEquals(array(), $this->object->getMessages());
    }
    public function testMethodGetMessagesShouldReturnArrayOfMessagesIfObjectInvalid()
    {
        $invalidData = array(
            'login'     => 'invalidLogin',
            'password'   => 'validPassword'
        );
        $errorMsg = 'Login is invalid';
        $validator = m::mock('Xboom\\Model\\Validate\\ValidatorInterface');
        $validator->shouldReceive('isValid')->once()->andReturn(false);
        $validator->shouldReceive('getMessages')->once()->andReturn(array($errorMsg));
        $this->object = new AbstractObject($invalidData);
        $this->object->setValidator($validator);
        $this->assertFalse($this->object->isValid());
        $this->assertContains($errorMsg, $this->object->getMessages());
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRaiseExceptionIfTryValidateNotArrayData()
    {
        $data = new stdClass();
        $this->object->isValid($data);
    }
}