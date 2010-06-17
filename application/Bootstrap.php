<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
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
        $front = $this->bootstrap('frontcontroller')->getResource('frontcontroller');

        $this->bootstrap('layout');

        $options = $this->getOption('view');

        // Initialize view
        $view = new Zend_View();
        $view->headTitle($options['title']);
        $view->doctype($options['doctype']);

        // for multilanguage
        // TODO: set base URL http://__HOST__/__BASE_URL__ without language in URL.
        $view->getHelper('BaseUrl')->setBaseUrl('http://xboom.local');
        // FIXME: add content language in output
//        $view->headMeta()
//             ->appendHttpEquiv('Content-Language', $locale);

        $view->assign('env', APPLICATION_ENV);

        // Add it to the ViewRenderer
        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        $viewRenderer->setView($view);
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);

        // Return it, so that it can be stored by the bootstrap
        return $view;
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
        if ($this->hasPluginResource('db')) {
            $this->bootstrap('db');
            $db = $this->getPluginResource('db')->getDbAdapter();
            $options['plugins']['Database']['adapter'] = $db;
        }

        # Setup the cache plugin
        if ($this->hasPluginResource('cache')) {
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