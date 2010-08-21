<?php

require_once 'PHPUnit/Framework.php';

require_once APPLICATION_PATH . '/../library/Xboom/Model/Domain/AbstractObject.php';

class AbstractObject extends Xboom_Model_Domain_AbstractObject
{
    protected $testPropertyTrue = true;
    protected $testPropertyFalse = true;
    protected $testProperty;
    protected $_testPropertyTrue = true;
    protected $testProtectedProperty;
    protected $_testProtectedProperty;

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
            "testProtectedProperty"=> null
        );
        $this->assertEquals($expected, $this->object->toArray());
    }
}