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

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    /**
     * Factory for init container.
     */
    protected $_containerFactory = null;

    /**
     * Set factory for resource container.
     *
     * @param array $options
     */
    protected function setDIContainer(array $options)
    {
        if (isset($options['factoryClass']))
        {
            $factory = $options['factoryClass'];
            $this->_containerFactory = new $factory($options['params']);
        }
    }

    /**
     * 
     * @return Object Container Factory 
     */
    public function getContainerFactory()
    {
        return $this->_containerFactory;
    }

    /**
     * Retrieve resource container
     * 
     * @return object
     */
    public function getContainer()
    {
        if (null === $this->_container)
        {
            if (null === $this->getContainerFactory())
            {
                $this->_container = parent::getContainer();
            }
            else
            {
                $this->_container = $this->getContainerFactory()->makeContainer();
            }
        }
        return $this->_container;
    }

    protected function _initAutoload()
    {
        $autoloader = Xboom\Loader\Autoloader::getInstance();
        $autoloader->registerNamespace($this->getOption('autoloaderNamespaces'));
        $nsSuffix = $this->getOption('appnamespace');
        if (!empty($nsSuffix))
        {
            $nsSuffix .= '\\';
        }
        $autoloader->registerNamespace(array(
            $nsSuffix . 'Core' => APPLICATION_PATH . '/modules/core',
            $nsSuffix . 'Core\\Model' => APPLICATION_PATH . '/modules/core/models',
            $nsSuffix . 'Core\\Model\\Service' => APPLICATION_PATH . '/modules/core/models/services',
            $nsSuffix . 'Core\\Model\\Form' => APPLICATION_PATH . '/modules/core/models/forms',
            $nsSuffix . 'Core\\Model\\Domain\\Validator'
                        => APPLICATION_PATH . '/modules/core/models/Domain/validators',
            )
        );
    }

    protected function _initEntityManager()
    {
        $sc = $this->getContainer();
        $options = $this->getOption('doctrine');
        $sc->addParameters(array(
            'doctrine.connection.options' => $options['connection'],
            'doctrine.orm.path_to_mappings' => $options['pathToMappings'],
            'doctrine.orm.path_to_entities' => $options['pathToEntities'],
            'doctrine.orm.path_to_proxies' => $options['pathToProxies'],
            'doctrine.orm.proxy_namespace' => $options['proxiesNamespace'],
            'doctrine.orm.autogenerate_proxy_classes'
            => $options['autogenerateProxyClasses'],
            'doctrine.common.cache_class' => $options['cacheClass'],
            'doctrine.common.cache_options' => $options['cacheOptions']
        ));
    }

    protected function _initPluginLoaderCache()
    {
        $classFileIncCache = APPLICATION_PATH . '/../data/cache/pluginLoaderCache.php';
        if (file_exists($classFileIncCache))
        {
            include_once $classFileIncCache;
        }
        if ($this->getOption("enablepluginloadercache"))
        {
            Zend_Loader_PluginLoader::setIncludeFileCache($classFileIncCache);
        }
    }

    /**
     *
     * @return Zend_View
     */
    protected function _initView()
    {
        $front = $this->bootstrap('frontcontroller');

        $options = $this->getOption('view');

        // Initialize view
        $view = new Zend_View($options);
        $view->headTitle($options['title']);
        $view->headTitle()->setSeparator($options['titleSeparator']);
        $view->doctype($options['doctype']);

        $view->headMeta()
                ->appendHttpEquiv('Content-Type',
                        'text/html;charset=' . strtolower($options['encoding']))
        // FIXME: add content language in output
        /* ->appendHttpEquiv('Content-Language', $locale) */;

        $view->assign('env', APPLICATION_ENV);

        // Add it to the ViewRenderer
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->setView($view);

        // Return it, so that it can be stored by the bootstrap
        return $view;
    }

    protected function _initResponse()
    {
        $this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');
        $response = new Zend_Controller_Response_Http;
        $response->setHeader('Content-Type', 'text/html; charset=UTF-8', true);
        $front->setResponse($response);
    }

    protected function _initZFDebug()
    {
        if (APPLICATION_ENV != 'development')
        {
            return;
        }

        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('ZFDebug');

        $options = array(
            'plugins' => array('Variables',
                'File' => array('base_path' => APPLICATION_PATH),
                'Memory',
                'Time',
                'Registry',
                'Exception')
        );

        # Instantiate the database adapter and setup the plugin.
        # Alternatively just add the plugin like above and rely on the autodiscovery feature.
        if ($this->hasPluginResource('db'))
        {
            $this->bootstrap('db');
            $db = $this->getPluginResource('db')->getDbAdapter();
            $options['plugins']['Database']['adapter'] = $db;
        }

        # Setup the cache plugin
        if ($this->hasPluginResource('cache'))
        {
            $this->bootstrap('cache');
            $cache = $this->getPluginResource('cache')->getDbAdapter();
            $options['plugins']['Cache']['backend'] = $cache->getBackend();
        }

        $debug = new ZFDebug_Controller_Plugin_Debug($options);

        $this->bootstrap('frontController');
        $frontController = $this->getResource('frontController');
        $frontController->registerPlugin($debug);
    }

}
