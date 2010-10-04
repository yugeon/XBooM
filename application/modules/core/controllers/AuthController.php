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
 * Description of AuthController
 *
 * @author yugeon
 */
class Core_AuthController extends Zend_Controller_Action
{
    /**
     *
     * @var sfServiceContainer
     */
    protected $_sc;
    /**
     * @var \Xboom\Model\Service\AuthService
     */
    protected $_authService;

    public function init()
    {
        $this->_sc = $this->getInvokeArg('bootstrap')->getContainer();
        $this->_authService = $this->_sc->getService('AuthService');
    }

    public function indexAction()
    {
        $this->_forward('login');
    }
    
    public function loginAction()
    {
        $this->view->messages = array();

        if ($this->_authService->hasIdentity())
        {
//            $this->view->messages[] = 'вы уже залогины '
//                    . $this->_authService->getCurrentUserIdentity()->getName();
            return;
        }

        if ($this->getRequest()->isPost())
        {
            $result = false;
            try
            {
                $result = $this->_authService
                        ->authenticate($this->getRequest()->getPost());
            }
            catch (Exception $e)
            {
                $this->view->messages[] = $e->getMessage();
            }

            if ($result)
            {
                // fixme redirect to a page where the request came.
                return $this->_redirect('/');
            }
            else
            {
                $this->view->messages = $this->_authService->getMessages();
            }
        }

    //    $this->view->loginForm = $this->_authService->getForm('LoginUser');
    }

    public function logoutAction()
    {
        $this->_authService->logout();
        $this->_redirect('/');
    }
}
