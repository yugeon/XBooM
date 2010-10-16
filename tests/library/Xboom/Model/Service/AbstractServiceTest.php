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
 * @author yugeon
 * @todo тесты медиатора
 */

namespace test\Xboom\Model\Service;
use \Xboom\Model\Service\AbstractService,
    \Xboom\Model\Domain\DomainObject,
    \Xboom\Model\Validate\AbstractValidator,
    \Mockery as m;

class TestService extends AbstractService
{
    public function _initService()
    {
        $this->setModelClassPrefix(__NAMESPACE__)
             ->setModelShortName('TestDomainObject')
             ->setValidatorClassPrefix(__NAMESPACE__)
             ->setFormClassPrefix(__NAMESPACE__);
    }

    public function __construct($sc)
    {
        parent::__construct($sc);
        $this->_initService();
    }
}

class TestDomainObject extends DomainObject
{

}

class TestDomainObjectValidator extends AbstractValidator
{

}

class RegisterForm extends \Zend_Form
{
    
}

class AbstractServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var TestService
     */
    protected $object;
    protected $sc;

    public function setUp()
    {
        parent::setUp();
        $em = m::mock('EntityManager');
        $this->sc = m::mock('ServiceContainer');
        $this->sc->shouldReceive('getService')->with('doctrine.orm.entitymanager')->andReturn($em);
        $this->object = new TestService($this->sc);
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }
    public function testCanCreateChildFromAbstractClass()
    {
        $this->assertNotNull($this->object);
    }

    public function testGetControlledModelName()
    {
        $this->assertTrue( \strlen($this->object->getModelShortName()) > 0 );
    }

    public function testGetControlledModelClassPrefix()
    {
        $this->assertTrue( \strlen($this->object->getModelClassPrefix()) > 0 );
    }

    public function testCanInjectUserObject()
    {
        $userModel = m::mock('\\Xboom\\Model\\Domain\\DomainObject');
        $this->object->setModel($userModel);
        $this->assertEquals($userModel, $this->object->getModel());
    }

    public function testMethodGetModelShouldReturnNewModelIfModelDontInit()
    {
        $this->assertNotNull($this->object->getModel());
    }

    public function  testCanGetDefaultValidator()
    {
        $this->assertNotNull($this->object->getValidator());
    }

    public function testMethodGetModelShouldSetDefaultValidator()
    {
        $this->assertNotNull($this->object->getModelWithValidator()->getValidator());
    }

    public function testMethodGetModelShouldSetSpecifiedValidator()
    {
        $validator = m::mock('\\Xboom\\Model\\Validate\\ValidatorInterface');
        $this->object->setValidator('MyValidator', $validator);

        $this->assertEquals($validator, $this->object->getModelWithValidator('MyValidator')->getValidator());
    }

    public function testGetForm()
    {
        $this->assertNotNull($this->object->getForm('Register'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShouldRaiseExceptionIfFormNotExist()
    {
        $this->assertNotNull($this->object->getForm('NotExistForm'));
    }

    public function testSetForm()
    {
        $formName = 'RegisterUser';
        $userForm = m::mock('Zend_Form');
        $this->object->setForm($formName, $userForm);
        $this->assertEquals($userForm, $this->object->getForm($formName));
    }

    public function testGetAclService()
    {
        $aclService = m::mock('AclService');
        $this->sc->shouldReceive('getService')->with('AclService')->andReturn($aclService);
        $this->assertEquals($aclService,
                $this->object->getServiceContainer()->getService('AclService'));
    }

    public function testGetFormWithModelValidators()
    {
        $formWithValidators = m::mock('Zend_Form');

        $mediator = m::mock('\\Xboom\\Model\\Form\\MediatorInterface');
        $mediator->shouldReceive('getFormWithValidators')->andReturn($formWithValidators);
        $this->object->setFormToModelMediator('My', $mediator);

        $this->assertEquals($formWithValidators, $this->object->getFormWithModelValidators('My'));
    }

    public function testGetFormWithValidatorAttribs()
    {
        $formWithValidatorAttribs = m::mock('Zend_Form');

        $mediator = m::mock('\\Xboom\\Model\\Form\\MediatorInterface');
        $mediator->shouldReceive('getFormWithAttribs')->andReturn($formWithValidatorAttribs);
        $this->object->setFormToModelMediator('My', $mediator);

        $this->assertEquals($formWithValidatorAttribs, $this->object->getFormWithValidatorAttribs('My'));
    }
}
