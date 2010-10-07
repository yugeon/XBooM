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
 * Description of LoginUser
 *
 * @author yugeon
 */

class Core_View_Helper_LoginWidget extends Zend_View_Helper_Abstract
{
    protected $_scriptPath = 'auth/login.phtml';

    /**
     * Set variables for user identity or login form and render view script.
     *
     * @param string $scriptPath
     * @return string
     */
    public function loginWidget($scriptPath = null)
    {
        if (null !== $scriptPath)
        {
            $this->_scriptPath = $scriptPath;
        }

        if (!isset($this->view->serviceContainer)
                || !is_object($this->view->serviceContainer))
        {
            return;
        }

        $_authService = $this->view->serviceContainer->getService('AuthService');

        $this->view->hasIdentity = $_authService->hasIdentity();

        if ($this->view->hasIdentity)
        {
            $this->view->userIdentity = $_authService->getCurrentUserIdentity();
            $this->view->loginForm = null;
        }
        else
        {
            $loginForm = $_authService->getForm('LoginUser');
            $loginForm->setAction(
                    $this->view->url(
                            array('controller' => 'auth', 'action' => 'login'))
            );
            $this->view->loginForm = $loginForm;
        }

        return $this->view->render($this->_scriptPath);
    }

    /**
     *
     * @return string
     */
    public function getScriptPath()
    {
        return $this->_scriptPath;
    }
}
