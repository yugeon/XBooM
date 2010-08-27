<?php

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
        $autoloader->registerNamespace(array(
            'Core' => APPLICATION_PATH . '/modules/core',
            'Core\\Model' => APPLICATION_PATH . '/modules/core/models',
            'Core\\Service' => APPLICATION_PATH . '/modules/core/models/services',
                )
        );
//        new Zend_Loader_Autoloader_Resource(array(
//                    'namespace' => 'Core',
//                    'basePath' => APPLICATION_PATH . '/modules/core',
//                    'resourceTypes' => array(
//                        'model' => array(
//                            'namespace' => 'Model',
//                            'path' => 'models'
//                        ),
//                        'service' => array(
//                            'namespace' => 'Service',
//                            'path' => 'models/services'
//                        ),
//                    )
//                ));
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
