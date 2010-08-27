<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    '.'
    // no current include_path for minimize autoloading
)));

/** Zend_Application */
require_once 'Zend/Application.php';

try
{
    // Create application, bootstrap, and run
    $application = new Zend_Application(
        APPLICATION_ENV,
        APPLICATION_PATH . '/configs/application.ini'
    );

    require_once 'Xboom/Loader/Autoloader.php';
    $autoloader = Xboom\Loader\Autoloader::getInstance();

    $application->bootstrap()
                ->run();
}
catch (Exception $exception)
{
    echo '<html><title>Critical Error</title><body><center>'
       . 'Technical work. Please try again later.';
    if (APPLICATION_ENV == 'development')
    {
        echo '<br /><br />An exception occured while bootstrapping the application.'
           . '<br />' . $exception->getMessage() . '<br />'
           . '<div align="left">Stack Trace:'
           . '<pre>' . $exception->getTraceAsString() . '</pre></div>';
    }
    echo '</center></body></html>';
    exit(1);
}