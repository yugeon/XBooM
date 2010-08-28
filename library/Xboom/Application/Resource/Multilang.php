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
 * Resource for initializing front controller plugin.
 *
 * @category   Multilang
 * @package    Multilang_Application
 * @subpackage Resources
 *
 * @author  yugeon
 * @version    SVN: $Id$
 */
class Xboom_Application_Resource_Multilang extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $options = $this->getOptions();
        $front = $this->getBootstrap()->bootstrap('frontcontroller')->getResource('frontcontroller');
        $plugin = new \Xboom\Controller\Plugin\Multilang(
                isset($options['default'])? $options['default'] : '',
                isset($options['locales'])? $options['locales'] : array(),
                isset($options['redirectIfLangNotPresent'])? $options['redirectIfLangNotPresent'] : false
                );
        // TODO stackIndex value from config?
        $stackIndex = null;
        $front->registerPlugin($plugin, $stackIndex);
    }
}