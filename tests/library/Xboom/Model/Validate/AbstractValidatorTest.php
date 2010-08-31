<?php
require_once 'PHPUnit/Framework.php';
require_once 'Mockery.php';

use Xboom\Model\Validate\Element\ValidatorInterface as ElementValidator;
use Xboom\Model\Validate\AbstractValidator;
use \Mockery as m;

class TestModelValidator extends AbstractValidator
{

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
        
        $this->object = new TestModelValidator();
        $this->loginValidator = m::mock('Xboom\\Model\\Validate\\Element\\ValidatorInterface');
        $this->loginValidator->shouldReceive('isValid')->andReturn(true);
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function  testSetAndGetPropertyValidator()
    {
        $propertyName = 'login';
        $this->object->setPropertyValidator($propertyName, $this->loginValidator);

        $this->assertEquals(
                $this->loginValidator,
                $this->object->getPropertyValidator($propertyName)
        );
    }
    /**
     * @expectedException \Xboom\Model\Validate\NoSuchPropertyException
     */
    public function testRaiseExceptionInRequestNotSettedValidator()
    {
        $this->object->getPropertyValidator('InvalidProperty');
    }
    public function  testValidDataMustReturnTrue()
    {
        $validData = array(
            'login'     => 'validLogin',
            'password'  => 'validPassword',
            'email'     => 'valid@email.com'
        );
        $this->object->setPropertyValidator('login', $this->loginValidator);
        
        $this->assertTrue($this->object->isValid($validData));
    }
    public function testInvalidDataMustReturnFalse()
    {
        $invalidData = array(
            'login'     => 'invalidLogin',
            'password'  => 'validPassword',
            'email'     => 'valid@email.com'
        );

        $loginValidator = m::mock('Xboom\\Model\\Validate\\Element\\ValidatorInterface');
        $loginValidator->shouldReceive('isValid')
                             ->with($invalidData['login'])->andReturn(false);
        $this->object->setPropertyValidator('login', $loginValidator);
        $this->assertFalse($this->object->isValid($invalidData));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function  testRaiseExceptionWhenNotArrayValidate()
    {
        $dataForValidation = new stdClass();
        $this->object->isValid($dataForValidation);
    }
    public function testGetMessageMustBeEmptyIfDataIsValid()
    {
        $validData = array(
            'login'     => 'validLogin',
            'password'  => 'validPassword',
            'email'     => 'valid@email.com'
        );
        $this->loginValidator->shouldReceive('getMessages')->andReturn(array());
        $this->object->setPropertyValidator('login', $this->loginValidator);

        $this->assertEquals(0, count($this->object->getMessages()));
    }
    public function testGetMessageMustReturnArrayOfMessagesIfDataIsNotValid()
    {
        $invalidData = array(
            'login'     => 'invalidLogin',
            'password'  => 'validPassword',
            'email'     => 'valid@email.com'
        );
        $errorMsg = 'Login must be valid!';

        $loginValidator = m::mock('Xboom\\Model\\Validate\\Element\\ValidatorInterface');
        $loginValidator->shouldReceive('isValid')
                             ->with($invalidData['login'])->andReturn(false);
        $loginValidator->shouldReceive('getMessages')->andReturn(array($errorMsg));
        $this->object->setPropertyValidator('login', $loginValidator);
        $this->assertFalse($this->object->isValid($invalidData));
        $this->assertContains($errorMsg, $this->object->getMessages());
    }
}
