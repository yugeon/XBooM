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

namespace test\App\Core\Model\Service;

use \Mockery as m,
 \App\Core\Model\Service\UserService,
 \App\Core\Model\Form\RegisterUserForm,
 \App\Core\Model\Domain\Validator\RegisterUserValidator;

/**
 * Description of User
 *
 * @author yugeon
 */
class UserTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @var App_Service_User
     */
    protected $object = null;
    /**
     *
     * @var \Doctrine\ORM\EntityManager 
     */
    protected $em;
    /**
     *
     * @var RegisterUserForm
     */
    protected $userMediator;
    /**
     *
     * @var \Xboom\Model\Domain\AbstractObject
     */
    protected $userModel;
    /**
     *
     * @var array
     */
    protected $userData;

    protected $acl;

    public function setUp()
    {
        parent::setUp();

        $this->em = m::mock('\\Doctrine\\ORM\\EntityManager');
        $this->em->shouldReceive('persist');
        $this->em->shouldReceive('flush');

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

        $this->userMediator = m::mock('\\Xboom\Model\\Form\\Mediator');
        $this->userMediator->shouldReceive('setDomainValidator')->andReturn($this->userMediator);

        $this->userModel = m::mock('\\Xboom\\Model\\Domain\\DomainObject');
        $this->userModel->shouldReceive('register');

        $this->object = new UserService($sc);
        $this->object->setModelClassPrefix('\\App\\Core\\Model\\Domain')
                ->setModelShortName('User')
                ->setValidatorClassPrefix('\\App\\Core\\Model\\Domain\\Validator')
                ->setFormClassPrefix('\\App\\Core\\Model\\Form');

        $testUserName = 'TestUserName' . rand(1, 100);
        $testUserPassword = md5($testUserName);
        $userData = array(
            'login' => $testUserName,
            'name' => $testUserName,
            'password' => $testUserPassword,
            'confirm_password' => $testUserPassword,
        );

        $this->userData = $userData;
        $this->userMediator->shouldReceive('getValues')->andReturn($userData);
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    // ---------------------------
    // Test UserService Operations
    // ---------------------------

    public function testGetUserById()
    {
        $this->em->shouldReceive('find')->once()->andReturn($this->userModel);

        $user = $this->object->getUserById(1);
        $this->assertEquals($this->userModel, $user);
    }

    public function testGetUsersList()
    {
        $result = array(
            $this->userModel,
            $this->userModel,
        );

        $this->em->shouldReceive('createQuery')->once()->andReturn($this->em);
        $this->em->shouldReceive('getResult')->once()->andReturn($result);

        $userList = $this->object->getUsersList();
        $this->assertTrue(is_array($userList));
        $this->assertEquals(2, count($userList));
    }

    public function testRegisterNewUser()
    {
        $this->acl->shouldReceive('isAllowed')->andReturn(true);

        $this->userMediator->shouldReceive('isValid')->andReturn(true);
        $this->userMediator->shouldReceive('getModel')->andReturn($this->userModel);

        $this->object->setFormToModelMediator('RegisterUser', $this->userMediator);

        $this->userModel = $this->object->registerUser($this->userData);

        $this->assertNotNull($this->userModel);
    }

    /**
     * @expectedException \Xboom\Model\Service\Acl\AccessDeniedException
     */
    public function testShouldRaiseExceptionIfNoPermissions()
    {
        $this->acl->shouldReceive('isAllowed')->andReturn(false);

        $user = $this->object->registerUser($this->userData);
    }

    /**
     * @expectedException \Xboom\Model\Service\Exception
     */
    public function testShouldRaiseExceptionIfCannotRegisterUserBecouseUserDataInvalid()
    {
        $this->acl->shouldReceive('isAllowed')->andReturn(true);
        // inject mock mediator
        $this->userMediator->shouldReceive('isValid')->andReturn(false);
        $this->object->setFormToModelMediator('RegisterUser', $this->userMediator);

        $user = $this->object->registerUser($this->userData);
    }
}
