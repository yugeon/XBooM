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
 * Description of DoctrineAdapter
 *
 * @author yugeon
 */

namespace Xboom\Auth\Adapter;

class Doctrine implements \Zend_Auth_Adapter_Interface
{

    const AUTH_FAILED = 'Authentication failed';

    /**
     *
     * @var string
     */
    protected $_entityName = null;
    /**
     *
     * @var string
     */
    protected $_identityName = null;
    /**
     *
     * @var string
     */
    protected $_identity = null;
    /**
     *
     * @var string
     */
    protected $_credential = null;
    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $_em = null;

    /**
     *
     * @param EntityManager $em
     * @param stirng $entityName
     * @param string $identityName
     */
    public function __construct($em = null, $entityName = null, $identityName = null)
    {
        $this->_em = $em;
        $this->setEntityName($entityName);
        $this->setIdentityName($identityName);
    }

    public function getEntityName()
    {
        return $this->_entityName;
    }

    public function setEntityName($entityName)
    {
        if (null !== $entityName)
        {
            $this->_entityName = (string) $entityName;
        }
        
        return $this;
    }

    public function getIdentityName()
    {
        return $this->_identityName;
    }

    public function setIdentityName($identityName)
    {
        if (null !== $identityName)
        {
            $this->_identityName = (string) $identityName;
        }
        
        return $this;
    }

    public function getIdentity()
    {
        return $this->_identity;
    }

    public function setIdentity($identity)
    {
        if (null !== $identity)
        {
            $this->_identity = (string) $identity;
        }
        return $this;
    }

    public function getCredential()
    {
        return $this->_credential;
    }

    public function setCredential($credential)
    {
        if (null !== $credential)
        {
            $this->_credential = (string) $credential;
        }
        return $this;
    }

    /**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        try
        {
            $this->_checkEnviroment();

            $entity = $this->_getEntity();

            if (null === $entity)
            {
                $result = $this->_createAuthResult(
                        \Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, null, array(self::AUTH_FAILED));
            }
            else
            {
                $authResult = $entity->authenticate($this->getCredential());
                if ($authResult)
                {
                    $result = $this->_createAuthResult(\Zend_Auth_Result::SUCCESS, $authResult);
                }
                else
                {
                    $result = $this->_createAuthResult(
                            \Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, null, array(self::AUTH_FAILED));
                }
            }
        }
        catch (\Exception $exc)
        {
            throw new \Zend_Auth_Adapter_Exception($exc->getMessage(), $exc->getCode(), $exc);
        }
        return $result;
    }

    /**
     * Validates the parameters passed
     *
     * @return boolean
     * @throws \InvalidArgumentException
     */
    protected function _checkEnviroment()
    {
        if (null === $this->_em)
        {
            throw new \InvalidArgumentException('Entity manager must be provided');
        }

        if (null === $this->getEntityName())
        {
            throw new \InvalidArgumentException('Entity name must be provided.');
        }

        if (null === $this->getIdentityName())
        {
            throw new \InvalidArgumentException('Identity name must be provided.');
        }

        if (null === $this->getIdentity())
        {
            throw new \InvalidArgumentException('Identity value must be provided.');
        }

        return true;
    }

    public function _getEntity()
    {
        $criteria = array(
            $this->getIdentityName() => $this->getIdentity()
        );

        return $this->_em->getRepository($this->getEntityName())
                        ->getForAuth($criteria);
    }

    protected function _createAuthResult($code, $identity = null, $messages = array())
    {
        return new \Zend_Auth_Result($code, $identity, $messages);
    }

}
