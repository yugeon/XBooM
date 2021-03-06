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
/**
 *
 * @author yugeon
 */

namespace Xboom\Model\Domain;

abstract class AbstractObject
{

    /**
     * Default constructor.
     * If $data exist, then assign to properties by key.
     * 
     * @param array $data 
     */
    public function __construct(array $data = null)
    {
        if (!is_null($data))
        {
            foreach ($data as $property => $value)
            {
                $accessor = 'set' . ucfirst($property);
                $this->{$accessor}($value);
            }
        }
    }

    /**
     * Map a call to get a property to its corresponding accessor if it exists.
     * Otherwise, get the property directly.
     * Ignore any properties that begin with an underscore so not all of our
     * protected properties are exposed.
     *
     * @param string $name Name of property
     * @return mixed
     * @throws \InvalidArgumenException If no accessor/property exists by that name
     */
    public function __get($name)
    {
        if ($name[0] !== '_')
        {
            $accessorMethod = 'get' . ucfirst($name);
            if (method_exists($this, $accessorMethod))
            {
                return $this->{$accessorMethod}();
            }

            if (property_exists($this, $name))
            {
                return $this->{$name};
            }
        }

        throw new \InvalidArgumentException('Property named ' . $name . ' dos\'t exists.');
    }

    /**
     * Map a call to set a property to its corresponding mutator if it exists.
     * Otherwise, set the property directly.
     *
     * Ignore any properties that begin with an underscore so not all of our
     * protected properties are exposed.
     *
     * @param  string $name Name of property
     * @param  mixed  $value Value of property
     * @return mixed Default return this object
     * @throws \InvalidArgumentException If no mutator/property exists by that name
     */
    public function __set($name, $value)
    {
        if ($name[0] !== '_')
        {
            $mutatorMethod = 'set' . ucfirst($name);
            if (method_exists($this, $mutatorMethod))
            {
                return $this->{$mutatorMethod}($value);
            }

            if (property_exists($this, $name))
            {
                $this->{$name} = $value;

                return $this;
            }
        }

        throw new \InvalidArgumentException('Property named ' . $name . ' dosn\'t exists.');
    }

    /**
     * Map a call to a non-existent mutator or accessor directly to its
     * corresponding property
     * Ignore any properties that begin with an underscore so not all of our
     * protected properties are exposed.
     *
     * @param  string $name
     * @param  array  $arguments
     * @return mixed
     * @throws \BadMethodCallException If no mutator/accessor can be found
     */
    public function __call($name, $arguments)
    {
        if (strlen($name) > 3)
        {
            $action = substr($name, 0, 3);
            $property = lcfirst(substr($name, 3));
            if ('_' != $property[0])
            {
                if ('set' == $action)
                {
                    $this->{$property} = array_shift($arguments);
                    return $this;
                }
                if ('get' == $action)
                {
                    return $this->{$property};
                }
            }
        }
        throw new \BadMethodCallException('No method named ' . $name . ' exists');
    }

    /**
     * Return all properties as array.
     * Ignore any properties that begin with an underscore.
     *
     * @return array
     */
    public function toArray()
    {
        $data = get_object_vars($this);
        $resultAsArray = array();
        foreach ($data as $property => $value)
        {
            if ('_' !== $property[0])
            {
                $resultAsArray[$property] = $value;
            }
        }
        return $resultAsArray;
    }
}
