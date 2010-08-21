<?php

ob_start();

error_reporting(E_ALL | E_STRICT);

// Define path to application directory
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));

// Define application environment
define('APPLICATION_ENV', 'testing');

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
            realpath(APPLICATION_PATH . '/../library'),
            get_include_path(),
        )));

// Setup autoloading for Zend Framework, and resources
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Xboom');
new Zend_Application_Module_Autoloader(array(
    'namespace' => '',
    'basePath' => APPLICATION_PATH
));

require_once 'ControllerTestCase.php';

// Autoloader for Mockery Framework
require_once 'Mockery/Loader.php';
$loader = new \Mockery\Loader;
$loader->register();