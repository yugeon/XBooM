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
     *
     * @param string $formName
     * @return object Form
     */
    public function getForm($formName)
    {
        if (isset($this->_forms[$formName]))
        {
            return $this->_forms[$formName];
        }
        else
        {
            // FIXME hardcode namespace
            $formClass = "\\Core\\Model\\Form\\{$formName}Form";
            if (\class_exists($formClass))
            {
                $form = new $formClass;
                $this->_forms[$formName] = $form;
                return $this->_forms[$formName];
            }
            throw new \InvalidArgumentException("Form '{$formName}' not found.");
        }
    }

    public function getValidator($validatorName)
    {
        if (isset($this->_validators[$validatorName]))
        {
            return $this->_validators[$validatorName];
        }
        else
        {
            // FIXME hardcode namespace
            $validatorClass = "\\Core\\Model\\Domain\\Validator\\{$validatorName}Validator";
            if (\class_exists($validatorClass))
            {
                $validator = new $validatorClass;
                $this->_validators[$validatorName] = $validator;
                return $this->_validators[$validatorName];
            }
            throw new \InvalidArgumentException("Validator '{$validatorName}' not found.");
        }
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
     * @return object User
     * @throws \Xboom\Exception If can't create new user
     */
    public function registerUser(array $data, $flush = true)
    {
        // TODO: ACL        !!!

        // TODO: REFACTORING засунуть все это в медиатор

        $registerUserForm = $this->getForm('RegisterUser');
        if ($registerUserForm->isValid($data))
        {
            $userData = $registerUserForm->getValues();
            $dataForReg = array(
                'name'     =>   $userData['name'],
                'login'    =>   $userData['login'],
                'password' =>   $userData['password'],
            );
            $user = new User($dataForReg);
            $userValidator = new \Core\Model\Domain\Validator\RegisterNewUserValidator();
            $user->setValidator($userValidator);

            if ($user->isValid())
            {
                $this->_em->persist($user);

                if ($flush)
                {
                    $this->_em->flush();
                }
                return $user;
            }
            else
            {
                $messages = $userValidator->getMessages();
                $formElements = $registerUserForm->getElements();
                foreach ($formElements as $key => $element)
                {
                    if (isset($messages[$key]))
                    {
                        $element->addErrors($messages[$key]);
                    }
                }
            }
        }

        throw new \Xboom\Exception('Can\'t create new user.');
    }

}
