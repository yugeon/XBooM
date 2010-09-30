<?php

require_once 'PHPUnit/Framework.php';
require_once 'Mockery.php';

use Xboom\Model\Validate\Element\ValidatorInterface as ElementValidator;
use Xboom\Model\Validate\AbstractValidator;
use \Mockery as m;

class TestModelValidator extends AbstractValidator
{

    protected static $testValidator;

    public static function setTestValidator($validator)
    {
        self::$testValidator = $validator;
    }

    public function init()
    {
        $propertyName = 'setInInitMethod';
        $this->addPropertyValidator($propertyName, self::$testValidator);
    }

}

/**
 * Description of AbstractTest
 *
 * @author yugeon
 */
class Xboom_Model_Validate_AbstractTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var TestModelValidator
     */
    protected $object;
    protected $loginValidator;

    public function setUp()
    {
        parent::setUp();

        $this->loginValidator = m::mock('Xboom\\Model\\Validate\\Element\\ValidatorInterface');
        $this->loginValidator->shouldReceive('isValid')->andReturn(true);
        $this->loginValidator->shouldReceive('getMessages')->andReturn(array());
        TestModelValidator::setTestValidator($this->loginValidator);
        $this->object = new TestModelValidator();
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testSetAndGetPropertyValidator()
    {
        $propertyName = 'login';
        $this->object->addPropertyValidator($propertyName, $this->loginValidator);

        $this->assertEquals(
                $this->loginValidator,
                $this->object->getPropertyValidator($propertyName)
        );
    }

    public function testMethodInitMustCall()
    {
        $this->assertEquals(
                $this->loginValidator,
                $this->object->getPropertyValidator('setInInitMethod')
        );
    }

    /**
     * @expectedException \Xboom\Model\Validate\NoSuchPropertyException
     */
    public function testRaiseExceptionInRequestNotSettedValidator()
    {
        $this->object->getPropertyValidator('InvalidProperty');
    }

    public function testValidDataMustReturnTrue()
    {
        $validData = array(
            'login' => 'validLogin',
            'password' => 'validPassword',
            'email' => 'valid@email.com'
        );
        $this->object->addPropertyValidator('login', $this->loginValidator);

        $this->assertTrue($this->object->isValid($validData));
    }

    public function testInvalidDataMustReturnFalse()
    {
        $invalidData = array(
            'login' => 'invalidLogin',
            'password' => 'validPassword',
            'email' => 'valid@email.com'
        );

        $loginValidator = m::mock('Xboom\\Model\\Validate\\Element\\ValidatorInterface');
        $loginValidator->shouldReceive('isValid')
                ->with($invalidData['login'])->andReturn(false);
        $this->object->addPropertyValidator('login', $loginValidator);
        $this->assertFalse($this->object->isValid($invalidData));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRaiseExceptionWhenNotArrayValidate()
    {
        $dataForValidation = new stdClass();
        $this->object->isValid($dataForValidation);
    }

    public function testGetMessageMustBeEmptyIfDataIsValid()
    {
        $validData = array(
            'login' => 'validLogin',
            'password' => 'validPassword',
            'email' => 'valid@email.com'
        );
        $this->loginValidator->shouldReceive('getMessages')->andReturn(array());
        $this->object->addPropertyValidator('login', $this->loginValidator);

        $this->assertEquals(0, count($this->object->getMessages()));
    }

    public function testGetMessageMustReturnArrayOfMessagesIfDataIsNotValid()
    {
        $invalidData = array(
            'login' => 'invalidLogin',
            'password' => 'validPassword',
            'email' => 'valid@email.com'
        );
        $errorMsg = 'Login must be valid!';

        $loginValidator = m::mock('Xboom\\Model\\Validate\\Element\\ValidatorInterface');
        $loginValidator->shouldReceive('isValid')
                ->with($invalidData['login'])->andReturn(false);
        $loginValidator->shouldReceive('getMessages')->andReturn(array($errorMsg));
        $this->object->addPropertyValidator('login', $loginValidator);
        $this->assertFalse($this->object->isValid($invalidData));
        $messages = $this->object->getMessages();
        $this->assertContains($errorMsg, $messages['login']);
    }

    public function testRequiredElement()
    {
        $validDataWithoutRequiredElement = array(
            //'login'     => 'validLogin',
            'password'  => 'validPassword',
            'email'     => 'valid@email.com'
        );
        $required = true;
        $errorMsg = TestModelValidator::REQUIRED_ELEMENT;
        $loginValidator = m::mock('Xboom\\Model\\Validate\\Element\\ValidatorInterface');
        $loginValidator->shouldReceive('addErrorMessage');
        $loginValidator->shouldReceive('getMessages')->andReturn(array($errorMsg));
        $this->object->addPropertyValidator('login', $loginValidator, $required);

        $this->assertFalse($this->object->isValid($validDataWithoutRequiredElement));

        $messages = $this->object->getMessages();
        $this->assertContains($errorMsg, $messages['login']);
    }

}
