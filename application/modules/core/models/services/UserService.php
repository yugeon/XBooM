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

use \Xboom\Model\Service\AbstractService,
 \Core\Model\Domain\User,
 \Xboom\Model\Service\Exception as ServiceException,
 \Xboom\Model\Service\Acl\AccessDeniedException;

class UserService extends AbstractService
{

    /**
     * Get all users as array of objects.
     *
     * @return array
     */
    public function getUsersList()
    {
        return $this->_em->createQuery('SELECT u FROM ' . $this->getModelFullName() . ' u')
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
        return $this->_em->find($this->getModelFullName(), (int) $userId);
    }

    /**
     * Register new user. Login must be present in $data.
     *
     * @param array $data
     * @param boolean $flush If true then flush EntityManager
     * @return object User
     * @throws \Xboom\Model\Service\Exception If can't create new user
     */
    public function registerUser(array $data, $flush = true)
    {
        $authService = $this->getServiceContainer()->getService('AuthService');
        $currentUserIdentity = $authService->getCurrentUserIdentity();

        $aclService = $this->getServiceContainer()->getService('AclService');
        $acl = $aclService->getAcl($currentUserIdentity->getRoles());

        if (! $acl->isAllowed($currentUserIdentity->getRoles(), 'Users', 'register'))
        {
            throw new AccessDeniedException('Access denied');
        }

        $formToModelMediator = $this->getFormToModelMediator('RegisterUser');
        $formToModelMediator->setDomainValidator($this->getValidator('UserDomain'));
        $breakValidation = false;
        if ($formToModelMediator->isValid($data, $breakValidation))
        {
            $user = $formToModelMediator->getModel();
            $user->register($formToModelMediator->getValues());

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
