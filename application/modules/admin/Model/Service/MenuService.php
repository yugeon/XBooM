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
 * Description of MenuService
 *
 * @author yugeon
 */
namespace App\Admin\Model\Service;
use \Xboom\Model\Service\AbstractService,
 \Xboom\Model\Service\Exception as ServiceException,
 \Xboom\Model\Service\Acl\AccessDeniedException;

class MenuService extends AbstractService
{

    /**
     * Add new menu.
     *
     * @param array $data
     * @param boolean $flush
     * @return Menu
     */
    public function addMenu(array $data, $flush = true)
    {
        // TODO refactoring (double code in every service)
        // acl
        $authService = $this->getServiceContainer()->getService('AuthService');
        $curUserIdentity = $authService->getCurrentUserIdentity();

        $aclService = $this->getServiceContainer()->getService('AclService');
        $acl = $aclService->getAcl($curUserIdentity->getRoles());

        if (! $acl->isAllowed($curUserIdentity->getRoles(), 'Users', 'register'))
        {
            throw new AccessDeniedException('Access denied');
        }

        // validation
        $formToModelMediator = $this->getFormToModelMediator('AddMenu');
        $formToModelMediator->setDomainValidator($this->getValidator('MenuDomain'));
        $breakValidation = false;
        if ($formToModelMediator->isValid($data, $breakValidation))
        {
            $data = $formToModelMediator->getValues();
            $menu = $this->getModel();
            $menu->add($data);
            
            $this->_em->persist($menu);

            if ($flush)
            {
                $this->_em->flush();
            }

            return $menu;
        }
        
        throw new ServiceException('Can\'t create new menu.');
    }
}
