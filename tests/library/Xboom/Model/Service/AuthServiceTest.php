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
 * Description of AuthServiceTest
 *
 * @author yugeon
 */

namespace test\Xboom\Model\Service;

use \Xboom\Model\Service\AuthService,
    \Mockery as m;

class AuthServiceTest extends \PHPUnit_Framework_TestCase
{

    protected $object;

    protected $authResult;
    protected $auth;
    protected $guestIdentity;

    public function setUp()
    {
        $this->guestIdentity = array(
            'name'  => 'guest',
            'email' => 'guest@guest',
            'roles' => array('1'),
        );

        $guest = m::mock('User');
        $guest->shouldReceive('getIdentity')->andReturn($this->guestIdentity);

        $em = m::mock('EntityManager');
        $em->shouldReceive('getRepository')->andReturn($em);
        $em->shouldReceive('findOneBy')->andReturn($guest);

        $sc = m::mock('ServiceContainer');
        $sc->shouldReceive('getService')
                ->with('doctrine.orm.entitymanager')
                ->andReturn($em);

        $this->badData = array(
            'email' => 'bad@email.com',
            'password' => 'badPassword'
        );
        
        $this->validData = array(
            'email' => 'good@email.com',
            'password' => 'validPassword'
        );

        $authAdapter = m::mock('Zend_Auth_Adapter_Interface');
        $authAdapter->shouldReceive('setEntityName')->andReturn($authAdapter);
        $authAdapter->shouldReceive('setIdentityName')->andReturn($authAdapter);
        $authAdapter->shouldReceive('setIdentity')->andReturn($authAdapter);
        $authAdapter->shouldReceive('setCredential')->andReturn($authAdapter);

        $authStorage = m::mock('Zend_Auth_Storage_Interface');
        $authStorage->shouldReceive('write');

        $this->authResult = m::mock('Zend_Auth_Result');
        $this->authResult->shouldReceive('getIdentity');

        $this->auth = m::mock('Zend_Auth');
        $this->auth->shouldReceive('authenticate')->andReturn($this->authResult);
        $this->auth->shouldReceive('getStorage')->andReturn($authStorage);
        $this->auth->shouldReceive('clearIdentity');

        $this->object = new AuthService($sc, $this->auth, $authAdapter);
        $this->object
                ->setModelClassPrefix('\\Model\\Domain')
                ->setModelShortName('User');
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testCanCreateAuthService()
    {
        $this->assertNotNull($this->object);
    }

    public function testFailedAuthenticateShouldReturnFalse()
    {
        $this->authResult->shouldReceive('isValid')->andReturn(false);
        $this->assertFalse($this->object->authenticate($this->badData));
    }

    public function testSuccefulAuthenticateShouldReturTrue()
    {
        $this->authResult->shouldReceive('isValid')->andReturn(true);
        $this->assertTrue($this->object->authenticate($this->validData));
    }

    public function testGetCurrentUserIdentityShouldReturnGuestIdentityIfUserNotLogin()
    {
        $expectedGuestIdentity = array(
            'name'  => 'guest',
            'email' => 'guest@guest',
            'roles' => array('1'),
        );

        $this->auth->shouldReceive('hasIdentity')->andReturn(false);

        $this->assertEquals($expectedGuestIdentity, $this->object->getCurrentUserIdentity());
    }

    public function testGetCurrentUserIdentityShouldReturnUserIdentityIfUserIsLogin()
    {
        $userIdentity = array(
            'name'  => 'Vasya',
            'email' => 'vasya@mail.com',
            'roles' => array('2', '3'),
        );

        $this->auth->shouldReceive('hasIdentity')->andReturn(true);
        $this->auth->shouldReceive('getIdentity')->andReturn($userIdentity);

        $expectedUserIdentity = $userIdentity;

        $this->assertEquals($expectedUserIdentity, $this->object->getCurrentUserIdentity());
    }

    public function testLogout()
    {
        $this->auth->shouldReceive('hasIdentity')->andReturn(false);
        $this->object->logout();

        $this->assertEquals($this->guestIdentity, $this->object->getCurrentUserIdentity());
    }
}
