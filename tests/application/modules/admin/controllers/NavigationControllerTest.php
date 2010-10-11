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
 * @group functional
 * @author yugeon
 */
class Admin_NavigationControllerTest extends ControllerTestCase
{
    public function testCanGetDefaultPage()
    {
        //$_SERVER['HTTP_HOST'] = 'xboom.local';
        $this->dispatch('/admin/navigation');
        $this->assertModule('admin');
        $this->assertController('navigation');
        $this->assertAction('index');
        $this->assertResponseCode(200);
    }

    public function testCanAddNewMenu()
    {
        $this->dispatch('/admin/navigation/add-menu');
        $this->assertModule('admin');
        $this->assertController('navigation');
        $this->assertAction('add-menu');
        $this->assertResponseCode(200);
    }
}
