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
 * Service to manage the menu pages.
 *
 * @author yugeon
 */
namespace App\Admin\Model\Service;
use \Xboom\Model\Service\AbstractService,
 \Xboom\Model\Service\Exception as ServiceException,
 \Xboom\Model\Service\Acl\AccessDeniedException;

class PageService extends AbstractService
{
    public function addPage(array $data, $flush = true)
    {
        // TODO refactoring (double code in every service)
        // acl
        $authService = $this->getServiceContainer()->getService('AuthService');
        $curUserIdentity = $authService->getCurrentUserIdentity();

        $aclService = $this->getServiceContainer()->getService('AclService');
        $acl = $aclService->getAcl($curUserIdentity->getRoles());

        if (! $acl->isAllowed($curUserIdentity->getRoles(), 'admin.page', 'add'))
        {
            throw new AccessDeniedException('Access denied');
        }

        // validation
        $formToModelMediator = $this->getFormToModelMediator('AddPage');
        $formToModelMediator->setDomainValidator($this->getValidator('PageDomain'));
        $breakValidation = false;
        if ($formToModelMediator->isValid($data, $breakValidation))
        {
            $data = $formToModelMediator->getValues();
            $page = $this->getModel();
            $page->add($data);

            $this->_em->persist($page);

            if ($flush)
            {
                $this->_em->flush();
            }

            return $page;
        }

        throw new ServiceException('Can\'t create new page.');
    }
}
