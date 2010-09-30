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
 * Description of AuthFunctionalTest
 *
 * @author yugeon
 */

namespace test\Xboom\Model\Service;

use \Core\Model\Domain\User,
    \Core\Model\Domain\Group,
    \Xboom\Model\Domain\Acl\Resource,
    \Xboom\Model\Domain\Acl\Permission,
    \Xboom\Model\Domain\Acl\Role;

/**
 * @group functional
 */
class AuthFunctionalTest extends \FunctionalTestCase
{

    /**
     *
     * @var \Xboom\Model\Service\AuthService
     */
    protected $authService;
    protected $newsResource;
    protected $viewPermission;
    protected $guestRole;
    protected $userRole;
    protected $guest;
    protected $user;

    public function setUp()
    {
        parent::setUp();
        
        $this->authService = $this->_sc->getService('AuthService');

        $this->guestRole = new Role();
        $this->guestRole->name = 'Role 1';
        $this->_em->persist($this->guestRole);

        $this->userRole = new Role();
        $this->userRole->name = 'Role 2';
        $this->_em->persist($this->userRole);

        // Guest group
        $guestGroup = new Group();
        $guestGroup->name = 'Guest group';
        $guestGroup->assignToRole($this->guestRole);
        $this->_em->persist($guestGroup);

        // User group
        $userGroup = new Group();
        $userGroup->name = 'User group';
        $userGroup->assignToRole($this->guestRole);
        $userGroup->assignToRole($this->userRole);
        $this->_em->persist($userGroup);

        // Guest account
        $this->guest = new User();
        $this->guest->name = 'guest';
        $this->guest->email = 'guest@guest';
        $this->guest->setGroup($guestGroup);
        $this->_em->persist($this->guest);

        // User account
        $this->user = new User();
        $this->user->name = 'Vasiliy Pupkin';
        $this->user->email = 'vasya@mail.com';
        $this->user->password = $this->user->encryptPassword('p@$$w0rd');
        $this->user->setGroup($userGroup);
        $this->_em->persist($this->user);

        $this->_em->flush();
    }

    public function testShouldReturnGuestIdentityIfUserNotLogin()
    {
        $expectedIdentity = array(
            'id'    => $this->guest->id,
            'name'  => $this->guest->name,
            'email' => $this->guest->email,
            'roles' => array($this->guestRole->id)
        );

        $this->assertEquals($expectedIdentity,
                $this->authService->getCurrentUserIdentity());
    }

    public function testAuthenticateFailedIfLoginInvalid()
    {
        $invalidData = array(
            'email' => 'bad@email.com',
            'password' => $this->user->encryptPassword('p@$$w0rd')
        );
        $this->assertFalse($this->authService->authenticate($invalidData));
    }

    public function testAuthenticateFailedIfPasswordInvalid()
    {
        $invalidData = array(
            'email' => $this->user->email,
            'password' => 'badPassword'
        );
        $this->assertFalse($this->authService->authenticate($invalidData));
    }

    public function testAuthenticateSuccesedIfDataValid()
    {
        $validData = array(
            'email' => $this->user->email,
            'password' => 'p@$$w0rd'
        );
        $this->assertTrue($this->authService->authenticate($validData));
    }

    public function testShouldReturnUserIdentityIfUserHasAlreadyLogged()
    {
        $expectedIdentity = array(
            'id'    => $this->user->id,
            'name'  => $this->user->name,
            'email' => $this->user->email,
            'roles' => array($this->guestRole->id, $this->userRole->id)
        );

        $validData = array(
            'email' => $this->user->email,
            'password' => 'p@$$w0rd'
        );

        $this->authService->authenticate($validData);

        $this->assertEquals($expectedIdentity,
                $this->authService->getCurrentUserIdentity());
    }

    public function testShouldReturnGuestIdentityIfUserLogouted()
    {
        $expectedIdentity = array(
            'id'    => $this->guest->id,
            'name'  => $this->guest->name,
            'email' => $this->guest->email,
            'roles' => array($this->guestRole->id)
        );

        $validData = array(
            'email' => $this->user->email,
            'password' => 'p@$$w0rd'
        );

        $this->authService->authenticate($validData);

        $this->authService->logout();

        $this->assertEquals($expectedIdentity,
                $this->authService->getCurrentUserIdentity());
    }

    public function testAuthenticateFiledShouldReturnArrayOfErrors()
    {
         $invalidData = array(
            'email' => $this->user->email,
            'password' => 'badPassword'
        );
        $this->authService->authenticate($invalidData);
        
        $this->assertContains('Authentication failed', $this->authService->getMessages());
    }

    public function testAuthenticateSuccessedShouldReturnEmptyArrayOfErrors()
    {
        $validData = array(
            'email' => $this->user->email,
            'password' => 'p@$$w0rd'
        );
        $this->authService->authenticate($validData);

        $this->assertTrue( \sizeof($this->authService->getMessages()) ==0 );
    }

}
