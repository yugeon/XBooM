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
 * Permission
 *
 * @category AccessControl
 * @author yugeon
 */
namespace Core\Model\Domain;
use \Xboom\Model\Domain\AbstractObject;

/**
 * @Entity
 * @Table(name="permissions")
 */
class Permission extends AbstractObject
{
    const DENY  = false;
    const ALLOW = true;

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     *
     * @var integer
     */
    protected $id;

    /**
     * Name of permission. Any word or value.
     *
     * @Column(type="string", nullable=true, length=50)
     * @var string
     */
    protected $name;

    /**
     * Allow or deny access to a resource. By default deny access.
     *
     * @Column(type="boolean")
     * @var boolean
     */
    protected $type = self::DENY;

    /**
     * The resource. Any object or something else that could be a resource.
     *
     * @ManyToOne(targetEntity="Resource")
     * @var Resource
     */
    protected $resource = null;
}
