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
namespace Core\Service;

use \Core\Model\Domain\User as User;

class UserService extends \Xboom\Service\AbstractService
{

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $_em;

    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->_em = $em;
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
     * @return App_Model_Domain_User 
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
     * @return User
     */
    public function registerNewUser(array $data, $flush = true)
    {
        // TODO: ACL        !!!
        // TODO: Validation !!!

        $user = new User($data);
        $this->_em->persist($user);

        if ($flush)
        {
            $this->_em->flush();
        }

        return $user;
    }

}
