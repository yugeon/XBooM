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
 * Description of AclFunctionalTestCase
 *
 * @author yugeon
 * @todo Множественные роли
 */
namespace test\Xboom\Model\Service\Acl;
use \Core\Model\Domain\User,
    \Core\Model\Domain\Group,
    \Xboom\Model\Domain\Acl\Resource,
    \Xboom\Model\Domain\Acl\Permission,
    \Xboom\Model\Domain\Acl\Role;

class AclFunctionalTest extends \FunctionalTestCase
{
    
    protected $role;
    protected $newsResource;
    protected $concreateNews;
    protected $viewPermission;
    protected $user;
    protected $aclService;

    public function setUp()
    {
        parent::setUp();

        $this->aclService = $this->_sc->getService('AclService');

        $this->_em->getConnection()->beginTransaction();

        $resourceOwner = new User();
        $resourceOwner->name = 'testName'. \rand(1, 100);
        $resourceOwner->email = 'test@mail.ru';
        $resourceOwner->password = \md5($resourceOwner->name);
        $this->_em->persist($resourceOwner);
        $this->user = $resourceOwner;

        $this->newsResource = new Resource();
        $this->newsResource->name = 'News';
        $this->_em->persist($this->newsResource);

        $confirmNewsResource = new Resource();
        $confirmNewsResource->name = 'Confirm';
        $confirmNewsResource->setParent($this->newsResource);
        $this->_em->persist($confirmNewsResource);

        $this->concreateNews = new Resource();
        $this->concreateNews->name = 'News 1';
        $this->concreateNews->setOwner($resourceOwner);
        $this->concreateNews->setParent($confirmNewsResource);
        $this->_em->persist($this->concreateNews);

        $this->viewPermission = new Permission();
        $this->viewPermission->name = 'view';
        $this->viewPermission->setTypeAllow();
        $this->viewPermission->setResource($this->newsResource);
        $this->_em->persist($this->viewPermission);
        
        $permission2 = new Permission();
        $permission2->name = 'edit';
        $permission2->setTypeAllow();
        $permission2->setIsOwnerRestriction(true);
        $permission2->setResource($confirmNewsResource);
        $this->_em->persist($permission2);

        $this->role = new Role();
        $this->role->name = 'Role 1';
        $this->role->assignToPermission($this->viewPermission);
        $this->role->assignToPermission($permission2);
        $this->_em->persist($this->role);

        $emptyRole = new Role();
        $emptyRole->name = 'Role 2';
        $this->_em->persist($emptyRole);

        $this->_em->flush();
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->_em->getConnection()->rollback();
        $this->_em->close();
    }

    public function testAllThatIsNotAllowedThenDenieded()
    {
        $acl = $this->aclService->getAcl();
        $testRole = 1;
        $this->assertFalse($acl->isAllowed($testRole));
    }

    public function testChekcFromFullAcl()
    {
        $acl = $this->aclService->getAcl();

        $this->assertTrue(
                $acl->isAllowed(
                        $this->role,
                        $this->newsResource,
                        $this->viewPermission->getName()
                )
        );
    }

    public function testCehckByRole()
    {
        $acl = $this->aclService->getAcl($this->role->getId());

        $this->assertTrue(
                $acl->isAllowed(
                        $this->role->getId(),
                        $this->newsResource,
                        $this->viewPermission->getName()
                )
        );

        $acl = $this->aclService->getAcl($this->role);

        $this->assertTrue(
                $acl->isAllowed(
                        $this->role,
                        $this->newsResource,
                        $this->viewPermission->getName()
                )
        );
    }

    public function testCehckByResource()
    {
        $acl = $this->aclService->getAcl(null, 'News');

        $this->assertTrue(
                $acl->isAllowed(
                        $this->role,
                        'News',
                        $this->viewPermission->getName()
                )
        );

        $acl = $this->aclService->getAcl(null, $this->newsResource);

        $this->assertTrue(
                $acl->isAllowed(
                        $this->role,
                        $this->newsResource,
                        $this->viewPermission->getName()
                )
        );
    }

    public function testCehckByPermission()
    {
        $acl = $this->aclService->getAcl(null, null, 'view');

        $this->assertTrue(
                $acl->isAllowed(
                        $this->role,
                        $this->newsResource,
                        'view'
                )
        );

        $acl = $this->aclService->getAcl(null, null, $this->viewPermission);

        $this->assertTrue(
                $acl->isAllowed(
                        $this->role,
                        $this->newsResource,
                        $this->viewPermission->getName()
                )
        );
    }

    public function testCehckByRolesAndResource()
    {
        $acl = $this->aclService->getAcl($this->role->getId(), 'News');

        $this->assertTrue(
                $acl->isAllowed(
                        $this->role->getId(),
                        'News',
                        'view'
                )
        );

        $acl = $this->aclService->getAcl($this->role, $this->newsResource);

        $this->assertTrue(
                $acl->isAllowed(
                        $this->role,
                        $this->newsResource,
                        $this->viewPermission->getName()
                )
        );
    }

    public function testCehckByRolesResourceAndPermission()
    {
        $acl = $this->aclService->getAcl($this->role->getId(), 'News', 'view');

        $this->assertTrue(
                $acl->isAllowed(
                        $this->role->getId(),
                        'News',
                        'view'
                )
        );

        $acl = $this->aclService->getAcl($this->role, $this->newsResource, $this->viewPermission);

        $this->assertTrue(
                $acl->isAllowed(
                        $this->role,
                        $this->newsResource,
                        $this->viewPermission->getName()
                )
        );
    }

    public function testInheritResource()
    {
        $acl = $this->aclService->getAcl($this->role, $this->concreateNews);

        $this->assertTrue(
                $acl->isAllowed(
                        $this->role,
                        $this->concreateNews,
                        'view'
                )
        );
    }

    public function testAssertionByOwner()
    {
        $acl = $this->aclService->getAcl($this->role);

        $this->assertTrue(
                $acl->isAllowed(
                        $this->role,
                        $this->concreateNews,
                        'edit',
                        $this->user
                )
        );

        $this->assertFalse(
                $acl->isAllowed(
                        $this->role,
                        $this->concreateNews,
                        'edit'
                )
        );
    }

}
