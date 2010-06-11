<?php

list($bms,$bs) = explode(' ', microtime());

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

    $application->bootstrap()
                ->run();
    list($ems, $es) = explode(' ', microtime());
    echo ($es + $ems) - ($bs + $bms);

}
catch (Exception $exception)
{
    echo '<html><title>Critical Error</title><body><center>'
       . 'An exception occured while bootstrapping the application.';
    if (APPLICATION_ENV == 'development') {
        echo '<br /><br />' . $exception->getMessage() . '<br />'
           . '<div align="left">Stack Trace:'
           . '<pre>' . $exception->getTraceAsString() . '</pre></div>';
    }
    echo '</center></body></html>';
    exit(1);
}