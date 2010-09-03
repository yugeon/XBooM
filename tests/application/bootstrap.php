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
require_once 'Xboom/Loader/Autoloader.php';
$autoloader = Xboom\Loader\Autoloader::getInstance();
$autoloader->registerNamespace('Xboom');
$autoloader->registerNamespace(array(
    'Core'              => APPLICATION_PATH . '/modules/core',
    'Core\\Model'       => APPLICATION_PATH . '/modules/core/models',
    'Core\\Model\\Service'     => APPLICATION_PATH . '/modules/core/models/services',
    'Core\\Model\\Form' => APPLICATION_PATH . '/modules/core/models/forms',
    'Core\\Model\\Domain\\Validator'
                        => APPLICATION_PATH . '/modules/core/models/Domain/validators',
    )
);

require_once 'ControllerTestCase.php';

// Autoloader for Mockery Framework
require_once 'Mockery/Loader.php';
$loader = new \Mockery\Loader;
$loader->register();