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

namespace Xboom\Application\Resource;
/**
 * Resource for initializing dependency injection container
 *
 * @package    Xboom
 * @subpackage Application_Resources
 *
 * @author     yugeon
 * @version    SVN: $Id$
 */
class DIContainerFactory
{

    /**
     * Contains the options for the factory.
     * @var array
     */
    protected  $_options;

    /**
     * Construct a factory that created containers for Zend_Bootstrap
     * based on the Symfony DI framework component.
     *
     * @param array $options Options for factory
     */
    public function __construct(array $options = array())
    {
        $this->_options = $options;
        /**
         * Must manually require here because the autoloader does not
         * (yet) know how to find this.
         */
        require_once 'Symfony/Components/DependencyInjection/sfServiceContainerAutoloader.php';
        \sfServiceContainerAutoloader::register();
    }

    public function setOptions(array $options = array())
    {
        $this->_options = array_merge($this->_options, $options);
    }

    /**
     * @return sfServiceContainerInterface The container
     */
    public function makeContainer()
    {
        $isGenerateDumpFile = $this->_options['enableAutogenerateDumpFile'];
        if (!$isGenerateDumpFile && file_exists($this->_options['dumpFilePath']))
        {
            require_once $this->_options['dumpFilePath'];
            $sc = new $this->_options['dumpFileClass']();
        }
        else
        {
            $sc = $this->buildServiceContainer();
            $this->dumpToFile($sc);
        }

        return $sc;
    }

    /**
     * Build the service container dynamically
     *
     * @return sfServiceContainer
     */
    protected function buildServiceContainer()
    {
        //
        $sc = new \sfServiceContainerBuilder();
        $file = $this->_options['configFile'];
        $suffix = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        switch ($suffix)
        {
            case 'xml':
                $loader = new \sfServiceContainerLoaderFileXml($sc);
                break;

            default:
                throw new \Zend_Exception("Invalid configuration file provided; unknown config type '$suffix'");
        }
        $loader->load($file);

        return $sc;
    }

    /**
     * Dump current service container to file.
     *
     * @param sfServiceContainer $sc
     */
    protected function dumpToFile($sc)
    {
        $dumper = new \sfServiceContainerDumperPhp($sc);
        $filename = $this->_options['dumpFilePath'];
        if (file_exists($filename))
        {
            // try delete file for rewrite
            unlink($filename);
        }
        file_put_contents($filename,
                $dumper->dump(array('class' => $this->_options['dumpFileClass']))
        );
    }
}