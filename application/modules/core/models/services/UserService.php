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
 * Description of User
 *
 * @author yugeon
 */

namespace Core\Model\Service;

use \Core\Model\Domain\User as User;
use \Xboom\Model\Service\Exception as ServiceException;
use \Xboom\Model\Form\Mediator;

class UserService extends \Xboom\Model\Service\AbstractService
{

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $_em;

    /**
     * Contain mediators beetwen forms and model.
     *
     * @var array
     */
    protected $_mediators;

    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->_em = $em;
    }

    /**
     * Return mediator by name if exist or try create it.
     *
     * @param string $mediatorName
     * @return object Mediator
     * @throws \InvalidArgumentException If form with $formName not exists.
     */
    public function getFormMediator($mediatorName)
    {
        if (isset($this->_mediators[$mediatorName]))
        {
            return $this->_mediators[$mediatorName];
        }

        $mediator = new Mediator($mediatorName);
        $this->setFormMediator($mediatorName, $mediator);

        return $this->_mediators[$mediatorName];
    }

    /**
     * For inject mediator.
     *
     * @param string $mediatorName
     * @param Zend_Form $mediator
     * @return UserService
     */
    public function setFormMediator($mediatorName, $mediator)
    {
        if (!\is_string($mediatorName))
        {
            throw new \InvalidArgumentException('Mediator name must be a string');
        }

        if (!($mediator instanceof \Xboom\Model\Form\MediatorInterface))
        {
            throw new \InvalidArgumentException('Mediator object must be an instance of MediatorInterface');
        }

        $this->_mediators[$mediatorName] = $mediator;

        return $this;
    }

    public function getForm($formName)
    {
        return $this->getFormMediator($formName)->getForm();
    }

    /**
     * Get all users as array of objects.
     *
     * @return array
     */
    public function getUsersList()
    {
        return $this->_em->createQuery('SELECT u FROM Core\\Model\\Domain\\User u')
                ->getResult();
    }

    /**
     * Return user by id. All relations not loaded!
     *
     * @param int $userId
     * @return oject User
     */
    public function getUserById($userId)
    {
        return $this->_em->find('Core\\Model\\Domain\\User', (int) $userId);
    }

    /**
     * Register new user. Login must be present in $data.
     *
     * @param array $data
     * @param boolean $flush If true then flush EntityManager
     * @return object User
     * @throws \Xboom\Service\Exception If can't create new user
     */
    public function registerUser(array $data, $flush = true)
    {
        // TODO: ACL !!!

        $formMediator = $this->getFormMediator('RegisterUser');
        if ($formMediator->isValid($data, false))
        {
            $user = new User($formMediator->getValues());

            //$user->register();

            $this->_em->persist($user);

            if ($flush)
            {
                $this->_em->flush();
            }
            
            return $user;
        }

        throw new ServiceException('Can\'t create new user.');
    }

}
