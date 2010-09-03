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

use Core\Model\Domain\User as User;
class Core_UserController extends Zend_Controller_Action
{
    /**
     *
     * @var Doctrine\ORM\EntityManager
     */
    protected  $em;

    public function init()
    {
        $sc = $this->getInvokeArg('bootstrap')->getContainer();
        $this->em = $sc->getService('doctrine.orm.entitymanager');
    }

    public function indexAction()
    {
        $results = $this->em->createQuery('SELECT u FROM Core\\Model\\Domain\\User u')
                            ->getResult();
        $this->view->users = $results;
    }

    public function addAction()
    {
        $userService = new Core\Model\Service\UserService($this->em);

        if ($this->getRequest()->isPost())
        {
            try
            {
                $result = $userService->registerUser($_POST);
                echo 'Register ok!';
            }
            catch (\Xboom\Exception $e)
            {
                echo 'Register Failed, try again';

            }
        }

        $form = $userService->getForm('RegisterUser');
        echo $form;

//        return $this->_helper->getHelper('Redirector')
//                             ->gotoUrl($this->getRequest()->getControllerName());
    }

    public function deleteAction()
    {
        $user = $this->em->find('Core\Model\Domain\User', (int) $this->_getParam('id'));

        $this->em->remove($user);
        $this->em->flush();

        return $this->_helper->getHelper('Redirector')
                             ->gotoUrl($this->getRequest()->getControllerName());
    }
}

