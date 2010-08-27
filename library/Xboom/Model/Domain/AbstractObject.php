<?php

namespace Xboom\Model\Domain;
/**
 *
 * @author yugeon
 */
abstract class AbstractObject
{

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

        throw new \InvalidArgumentException('No property named ' . $name . ' exists.');
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

        throw new \InvalidArgumentException('No property named ' . $name . ' exists.');
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
