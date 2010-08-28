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

namespace Xboom\Loader;

/**
 * Description of Autoloader
 *
 * @author yugeon
 */
class Autoloader
{
    const NS_SEPARATOR = '\\';
    const PREFIX_SEPARATOR = '_';

    /**
     * @var Xboom\Loader\Autoloader Singleton instance
     */
    protected static $_instance = null;
    /**
     * @var array Supported namespaces 'Zend' and 'ZendX' by default.
     */
    protected static $_namespaces = array(
        'Zend' => '',
        'ZendX' => '',
    );

    /**
     * Helper method to calculate the correct class path
     *
     * @param string $class
     * @return False if not matched other wise the correct path
     */
    public static function getClassPath($class)
    {
        $separator = self::PREFIX_SEPARATOR;
        if (false !== strpos($class, self::NS_SEPARATOR))
        {
            //if exist php 5.3 namespace separator
            $separator = self::NS_SEPARATOR;
        }

        $segments = explode($separator, $class);
        if (count($segments) < 2)
        {
            // assumes all resources have a component and class name, minimum
            return false;
        }

        $final = array_pop($segments);
        $component = '';
        $lastMatch = false;
        do
        {
            $segment = array_shift($segments);
            $component .= empty($component) ? $segment : $separator . $segment;
            if (isset(self::$_namespaces[$component]))
            {
                $lastMatch = $component;
            }
        }
        while (count($segments));

        if (!$lastMatch)
        {
            return false;
        }

        $final = substr($class, strlen($lastMatch) + 1);
        if (empty(self::$_namespaces[$lastMatch]))
        {
            $path = str_replace($separator, DIRECTORY_SEPARATOR, $lastMatch)
                    . DIRECTORY_SEPARATOR;
        }
        else
        {
            $path = self::$_namespaces[$lastMatch];
        }

        $classPath = $path
                    . str_replace($separator, DIRECTORY_SEPARATOR, $final) . '.php';
        return $classPath;
    }

    /**
     * Autoload a class
     *
     * @param   $class
     * @return  mixed
     *          False [if unable to load $class]
     *          get_class($class) [if $class is successfully loaded]
     */
    public static function autoload($class)
    {
        $classPath = self::getClassPath($class);
        if (false !== $classPath)
        {
            return include $classPath;
        }
        return false;
    }

    /**
     * Retrieve singleton instance
     *
     * @return Zend_Loader_Autoloader
     */
    public static function getInstance()
    {
        if (null === self::$_instance)
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     *
     * Registers instance with spl_autoload stack
     *
     * @return void
     */
    protected function __construct()
    {
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    /**
     * Reset the singleton instance
     *
     * @return void
     */
    public static function resetInstance()
    {
        self::$_instance = null;
    }

    /**
     * Get a list of registered autoload namespaces
     *
     * @return array
     */
    public function getRegisteredNamespaces()
    {
        return array_keys(self::$_namespaces);
    }

    /**
     * Register a namespace/directory pair
     *
     * @param string|array $namespace
     * @param string $directory Default empty string (use include_path)
     */
    public function registerNamespace($namespace, $directory = '')
    {
        if (is_string($namespace))
        {
            $namespace = array($namespace => $directory);
        }
        elseif (!is_array($namespace))
        {
            throw new \InvalidArgumentException('Invalid namespace provided');
        }

        foreach ($namespace as $ns => $dir)
        {
            $ns = rtrim($ns, self::PREFIX_SEPARATOR . self::NS_SEPARATOR);
            self::$_namespaces[$ns] = $this->normalizeDirectory($dir);
        }
    }

    /**
     * Unload a registered autoload namespace
     *
     * @param  string|array $namespace
     */
    public function unregisterNamespace($namespace)
    {
        if (is_string($namespace))
        {
            $namespace = (array) $namespace;
        }
        elseif (!is_array($namespace))
        {
            throw new \InvalidArgumentException('Invalid namespace provided');
        }

        foreach ($namespace as $ns)
        {
            $ns = rtrim($ns, self::PREFIX_SEPARATOR . self::NS_SEPARATOR);
            if (isset(self::$_namespaces[$ns]))
            {
                unset(self::$_namespaces[$ns]);
            }
        }
    }

    /**
     * Normalize the directory to include a trailing directory separator
     * If empty directory then use include_path
     *
     * @param string $directory
     * @return string
     */
    protected function normalizeDirectory($directory)
    {
        if ('' === $directory)
        {
            return '';
        }

        $last = $directory[strlen($directory) - 1];
        if (in_array($last, array('/', '\\')))
        {
            $directory[strlen($directory) - 1] = DIRECTORY_SEPARATOR;
            return $directory;
        }
        $directory .= DIRECTORY_SEPARATOR;
        return $directory;
    }

}
