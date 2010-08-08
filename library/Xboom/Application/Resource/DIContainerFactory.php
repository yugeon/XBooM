<?php
/**
 * Resource for initializing dependency injection container
 *
 * @package    Xboom
 * @subpackage Application_Resources
 *
 * @author     yugeon
 * @version    SVN: $Id$
 */
class Xboom_Application_Resource_DIContainerFactory
{
    /**
     * Contains the options for the factory.
     * @var array
     */
    private $_options;

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
        sfServiceContainerAutoloader::register();
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
        if ($this->_options['enableDumpFile']
                && file_exists($this->_options['dumpFilePath']))
        {
            require_once $this->_options['dumpFilePath'];
            $sc = new $this->_options['dumpFileClass']();
        }
        else
        {
            // build the service container dynamically
            $sc = new sfServiceContainerBuilder();
            $file = $this->_options['configFile'];
            $suffix = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            switch ($suffix)
            {
                case 'xml':
                    $loader = new sfServiceContainerLoaderFileXml($sc);
                    break;

/**                case 'yml':
                    $loader = new sfServiceContainerLoaderFileYaml($sc);
                    break;

                case 'ini':
                    $loader = new sfServiceContainerLoaderFileIni($sc);
                    break;
*/
                default:
                    throw new Zend_Exception("Invalid configuration file provided; unknown config type '$suffix'");
            }
            $loader->load($file);

            if ($this->_options['enableDumpFile'])
            {
                $dumper = new sfServiceContainerDumperPhp($sc);
                file_put_contents($this->_options['dumpFilePath'],
                        $dumper->dump(array('class' => $this->_options['dumpFileClass']))
                );
            }
        }

        return $sc;
    }
}