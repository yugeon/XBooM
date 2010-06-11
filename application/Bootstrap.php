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

}