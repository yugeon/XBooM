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

class Admin_NavigationController extends Zend_Controller_Action
{

    protected $_menuService;

    public function init()
    {
        // fixme set admin settings in one place
        $this->_helper->layout->setLayout('admin');
        
        $sc = $this->getInvokeArg('bootstrap')->getContainer();
        $this->_menuService = $sc->getService('MenuService');
    }

    public function indexAction()
    {
        $this->view->menuList = array();
        try
        {
            $this->view->menuList = $this->_menuService->getMenuList();
        }
        catch (\Xboom\Model\Service\Acl\AccessDeniedException $e)
        {
            $this->view->messages = (array)$e->getMessage();
        }
    }

    // todo refactoring
    public function addMenuAction()
    {
        $messages = array();

        if ($this->getRequest()->isPost())
        {
            try
            {
                $result = $this->_menuService->addMenu($this->getRequest()->getPost());
                $messages = 'Add ok! Id: ' . $result->id;
            }
            catch (\Xboom\Model\Service\Acl\AccessDeniedException $e)
            {
                $messages = $e->getMessage();
            }
            catch (\Xboom\Model\Service\Exception $e)
            {
                $messages = 'Add Failed, try again';
            }
        }

        $this->view->messages = (array) $messages;
        $this->view->form = $this->_menuService->getForm('AddMenu');
    }

}
