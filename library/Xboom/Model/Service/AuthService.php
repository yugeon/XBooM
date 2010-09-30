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
 * Description of AuthService
 *
 * @author yugeon
 */

namespace Xboom\Model\Service;

class AuthService extends AbstractService
{

    /**
     *
     * @var Zend_Auth
     */
    protected $_auth = null;
    /**
     *
     * @var Zend_Auth_Adapter_Interface
     */
    protected $_authAdapter = null;

    /**
     *
     * @var Zend_Auth_Result
     */
    protected $_result = null;

    public function __construct($sc, $auth, $authAdapter)
    {
        parent::__construct($sc);
        $this->_auth = $auth;
        $this->_authAdapter = $authAdapter;
    }

    public function getAuthInstance()
    {
        return $this->_auth;
    }

    public function getCode()
    {
        $code = null;

        if (null !== $this->_result)
        {
            $code = $this->_result->getCode();
        }
        
        return $code;
    }

    public function getMessages()
    {
        $messages = array();

        if (null !== $this->_result)
        {
            $messages = $this->_result->getMessages();
        }

        return $messages;
    }

    public function authenticate($data)
    {
        // TODO ACL
        // TODO Validation

        $this->_authAdapter
                ->setIdentity($data['email'])
                ->setCredential($data['password']);
        $this->_result = $this->_auth->authenticate($this->_authAdapter);

        return $this->_result->isValid();
    }

    public function getGuestIdentity()
    {
        $result = array();
        
        // FIXME caching
        $guest = $this->_em
                ->getRepository($this->getModelFullName())
                        //fixme hardcode guest identity by email
                        ->findOneBy(array('email' => 'guest@guest'));

        if (null !== $guest)
        {
            $result = $guest->getIdentity();
        }
        return $result;
    }

    public function getCurrentUserIdentity()
    {
        if ($this->_auth->hasIdentity())
        {
            $identity = $this->_auth->getIdentity();
        }
        else
        {
            $identity = $this->getGuestIdentity();
        }

        return $identity;
    }

    public function logout()
    {
        $this->_auth->clearIdentity();
    }

}
