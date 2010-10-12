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

use \App\Admin\Model\Service\MenuService,
 \Mockery as m;

class MenuServiceTest extends \PHPUnit_Framework_TestCase
{

    protected $object;
    protected $em;
    protected $acl;
    protected $data;
    protected $addValidator;

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

        $sc = m::mock('ServiceContainer');
        $sc->shouldReceive('getService')->with('doctrine.orm.entitymanager')->andReturn($this->em);
        $sc->shouldReceive('getService')->with('AuthService')->andReturn($authService);
        $sc->shouldReceive('getService')->with('AclService')->andReturn($aclService);

        $this->object = new MenuService($sc);
        $this->object->setModelClassPrefix('\\App\\Core\\Model\\Domain\\Navigation')
                ->setModelShortName('Menu')
                ->setValidatorClassPrefix('\\App\\Core\\Model\\Domain\\Validator')
                ->setFormClassPrefix('\\App\\Core\\Model\\Form');

        $this->data = array(
            'good' => array('name' => 'testMenu'),
            'bad' => array('name' => 'badMenu'),
        );

        $addForm = m::mock('Zend_Form');
        $addForm->shouldReceive('isValid')->andReturn(true);
        $addForm->shouldReceive('getElements')->andReturn(array());
        $addForm->shouldReceive('getValues')->andReturn($this->data['good']);

        $this->addValidator = m::mock('\\Xboom\\Model\\Validate\\ValidatorInterface');
        $this->addValidator->shouldReceive('getValues')->andReturn($this->data['good']);
        $this->addValidator->shouldReceive('getPropertiesForValidation')->andReturn(array());
        $this->addValidator->shouldReceive('getMessages')->andReturn(array());

        $this->object->setForm('AddMenu', $addForm);
        $this->object->setValidator('AddMenu', $this->addValidator);
        $this->object->setValidator('MenuDomain', $this->addValidator);
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

    public function testCanAddNewMenu()
    {
        $this->acl->shouldReceive('isAllowed')->andReturn(true);
        $this->addValidator->shouldReceive('isValid')->andReturn(true);
        $this->assertType('\\Xboom\\Model\\Domain\\DomainObject',
                $this->object->addMenu($this->data['good']));
    }

    /**
     * @expectedException \Xboom\Model\Service\Acl\AccessDeniedException
     */
    public function testShouldRaiseExceptionIfNoPermissions()
    {
        $this->acl->shouldReceive('isAllowed')->andReturn(false);
        $user = $this->object->addMenu($this->data['good']);
    }

    /**
     * @expectedException \Xboom\Model\Service\Exception
     */
    public function testShouldRaiseExceptionsIfValidationFailure()
    {
        $this->acl->shouldReceive('isAllowed')->andReturn(true);
        $this->addValidator->shouldReceive('isValid')->andReturn(false);

        $this->object->addMenu($this->data['bad']);
    }

    public function testCanGetMenuList()
    {
        $menu = m::mock('Menu');
        $expectedMenuList = array(
            $menu,
            $menu,
            $menu,
        );

        $this->acl->shouldReceive('isAllowed')->andReturn(true);
        $this->em->shouldReceive('getResult')->andReturn($expectedMenuList);

        $this->assertEquals($expectedMenuList, $this->object->getMenuList());
    }

    /**
     * @expectedException \Xboom\Model\Service\Acl\AccessDeniedException
     */
    public function testGetMenuListShouldRaiseExceptionIfNoPermissions()
    {
        $this->acl->shouldReceive('isAllowed')->andReturn(false);
        $user = $this->object->getMenuList();
    }
}