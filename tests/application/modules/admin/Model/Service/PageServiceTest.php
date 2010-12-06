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


namespace test\App\Admin\Model\Service;

use \App\Admin\Model\Service\PageService,
 \Mockery as m;

class PageServiceTest extends \PHPUnit_Framework_TestCase
{

    protected $object;

    public function setUp()
    {
        $this->em = m::mock('\\Doctrine\\ORM\\EntityManager');
        $this->em->shouldReceive('persist');
        $this->em->shouldReceive('flush');
        $this->em->shouldReceive('createQuery')->andReturn($this->em);

        $this->acl = m::mock('Zend_Acl');

        $userIdentity = m::mock('UserIdentity');
        $userIdentity->shouldReceive('getRoles')->andReturn(array());

        $authService = m::mock('AuthService');
        $authService->shouldReceive('getCurrentUserIdentity')->andReturn($userIdentity);

        $aclService = m::mock('AclService');
        $aclService->shouldReceive('getAcl')->andReturn($this->acl);

        $menuEntity = m::mock('Menu');
        $menuEntity->shouldReceive('assignToPage')->andReturn(true);

        $menuService = m::mock('MenuService');
        $menuService->shouldReceive('getMenuByName')->andReturn($menuEntity);

        $sc = m::mock('ServiceContainer');
        $sc->shouldReceive('getService')->with('doctrine.orm.entitymanager')->andReturn($this->em);
        $sc->shouldReceive('getService')->with('AuthService')->andReturn($authService);
        $sc->shouldReceive('getService')->with('AclService')->andReturn($aclService);
        $sc->shouldReceive('getService')->with('MenuService')->andReturn($menuService);

        $this->object = new PageService($sc);

        $this->object->setModelClassPrefix('\\App\\Core\\Model\\Domain\\Navigation')
                ->setModelShortName('Page')
                ->setValidatorClassPrefix('\\App\\Admin\\Model\\Domain\\Validator')
                ->setFormClassPrefix('\\App\\Admin\\Model\\Form');

        $resource = m::mock('Resource');
        $permission = m::mock('Permission');
        
        $this->data = array(
            'uri' => array(
                'menuName' => 'Test menu',
                'label' => 'Home',
                'class' => 'menu-item',
                'title' => 'Home page',
                'target' => '_blank',
                'type' => 'uri',
                'order' => 1,
                'module' => 'core',
                'controller' => 'index',
                'action' => 'index',
                'params' => array('param1' => 'value1', 'param2' => 'value2'),
                'route' => null,
                'resetParams' => true,
                'uri' => 'http://google.com/?a=b',
                'isActive' => true,
                'isVisible' => true,
                'resource' => $resource,
                'privilege' => $permission,
                'pages' => array()
            ),
            'mvc' => array(
                'menuName' => 'Test menu',
                'label' => 'Home',
                'class' => 'menu-item',
                'title' => 'Home page',
                'target' => '_blank',
                'type' => 'mvc',
                'order' => 1,
                'module' => 'core',
                'controller' => 'index',
                'action' => 'index',
                'params' => array('param1' => 'value1', 'param2' => 'value2'),
                'route' => null,
                'resetParams' => true,
                'uri' => 'http://google.com/?a=b',
                'isActive' => true,
                'isVisible' => true,
                'resource' => $resource,
                'privilege' => $permission,
                'pages' => array()
            ),
            'bad' => array(
                'menuName' => 'Test menu',
                'label' => 'Home',
                'class' => 'menu-item',
                'title' => 'Home page',
                'target' => '_blank',
                'type' => 'uri',
                'order' => 1,
                'module' => 'core',
                'controller' => 'index',
                'action' => 'index',
                'params' => array('param1' => 'value1', 'param2' => 'value2'),
                'route' => null,
                'resetParams' => true,
                'uri' => 'http://google.com/?a=b',
                'isActive' => true,
                'isVisible' => true,
                'resource' => $resource,
                'privilege' => $permission,
                'pages' => array()
            ),
        );

        $addForm = m::mock('Zend_Form');
        $addForm->shouldReceive('isValid')->andReturn(true);
        $addForm->shouldReceive('getElements')->andReturn(array());
        $addForm->shouldReceive('getValues')->andReturn($this->data['mvc']);

        $this->addValidator = m::mock('\\Xboom\\Model\\Validate\\ValidatorInterface');
        $this->addValidator->shouldReceive('getValues')->andReturn($this->data['mvc']);
        $this->addValidator->shouldReceive('getPropertiesForValidation')->andReturn(array());
        $this->addValidator->shouldReceive('getMessages')->andReturn(array());

        $this->object->setForm('AddPage', $addForm);
        $this->object->setValidator('AddPage', $this->addValidator);
        $this->object->setValidator('PageDomain', $this->addValidator);
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

    public function testCanAddNewPage()
    {
        $this->acl->shouldReceive('isAllowed')->andReturn(true);
        $this->addValidator->shouldReceive('isValid')->andReturn(true);
        $result = $this->object->addPage($this->data['mvc']);
        $this->assertType('\\Xboom\\Model\\Domain\\DomainObject', $result);
        $this->assertEquals($this->data['mvc']['label'], $result->getLabel());
    }

    /**
     * @expectedException \Xboom\Model\Service\Acl\AccessDeniedException
     */
    public function testShouldRaiseExceptionIfNoPermissionsThenTryAddPage()
    {
        $this->acl->shouldReceive('isAllowed')->andReturn(false);
        $user = $this->object->addPage($this->data['mvc']);
    }

    /**
     * @expectedException \Xboom\Model\Service\Exception
     */
    public function testShouldRaiseExceptionsIfValidationFailureThenTryAddPage()
    {
        $this->acl->shouldReceive('isAllowed')->andReturn(true);
        $this->addValidator->shouldReceive('isValid')->andReturn(false);

        $this->object->addPage($this->data['bad']);
    }
}