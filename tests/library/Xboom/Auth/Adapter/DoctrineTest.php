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


namespace test\Xboom\Auth\Adapter;

use \Xboom\Auth\Adapter\Doctrine,
    \Mockery as m;

class DoctrineTest extends \PHPUnit_Framework_TestCase
{

    protected $object;
    protected $entityName = '\\Model\\Domain\\User';

    protected $nonExistIdentityCriteria;
    protected $existIdentityCriteria;


    public function setUp()
    {
        $this->nonExistIdentityCriteria = array(
            'email' => 'nonExistIdentity'
        );
        $this->existIdentityCriteria = array(
            'email' => 'ExistIdentity'
        );

        $identity = m::mock('stdClass');

        $user = m::mock('User');
        $user->shouldReceive('authenticate')->with(null)->andReturn(false);
        $user->shouldReceive('authenticate')->with('validPass')->andReturn($identity);

        $em = m::mock('\\Doctrine\\ORM\\EntityManager');
        $em->shouldReceive('getRepository')->andReturn($em);
        $em->shouldReceive('getForAuth')->with($this->nonExistIdentityCriteria)->andReturn(null);
        $em->shouldReceive('getForAuth')->with($this->existIdentityCriteria)->andReturn($user);

        $this->object = new Doctrine($em);
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testCanCreateAdapter()
    {
        $this->assertNotNull($this->object);
    }

    public function testShouldImplementsZendAuthAdapterInterface()
    {
        $this->assertType('Zend_Auth_Adapter_Interface', $this->object);
    }

    /**
     * @expectedException \Zend_Auth_Adapter_Exception
     */
    public function testShouldRaiseExceptionIfAuthentificateIsImpossible()
    {
        $this->object->authenticate();
    }

    public function testAuthentificateFailedIfIdentityNotFound()
    {
        $this->object->setEntityName($this->entityName)
                     ->setIdentityName('email')
                     ->setIdentity($this->nonExistIdentityCriteria['email']);

        $this->assertEquals(\Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND,
                $this->object->authenticate()->getCode());

        $this->assertContains('Authentication failed',
                $this->object->authenticate()->getMessages());
    }

    public function testAuthentificateFailedIfCredentialInvalid()
    {
        $this->object->setEntityName($this->entityName)
                     ->setIdentityName('email')
                     ->setIdentity($this->existIdentityCriteria['email']);

        $this->assertEquals(\Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
                $this->object->authenticate()->getCode());

        $this->assertContains('Authentication failed',
                $this->object->authenticate()->getMessages());
    }

    public function testAuthenticateSuccessed()
    {
        $this->object->setEntityName($this->entityName)
                     ->setIdentityName('email')
                     ->setIdentity($this->existIdentityCriteria['email'])
                     ->setCredential('validPass');

        $this->assertEquals(\Zend_Auth_Result::SUCCESS,
                $this->object->authenticate()->getCode());

        $this->assertTrue(sizeof($this->object->authenticate()->getMessages()) === 0);
    }

    public function testShouldReturnIdentityIfAuthenticateSuccessed()
    {
        $this->object->setEntityName($this->entityName)
                     ->setIdentityName('email')
                     ->setIdentity($this->existIdentityCriteria['email'])
                     ->setCredential('validPass');

        $this->assertNotNull($this->object->authenticate()->getIdentity());
    }
}
