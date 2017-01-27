<?php

namespace Draw\SwaggerGeneratorBundle\Generator;

/**
 * @author Martin Poirier Theoret <mpoiriert@gmail.com>
 */
class Registry extends \ArrayObject
{
    public function set($name, $value)
    {
        $this[$name] = $value;
        return $this;
    }

    public function get($name)
    {
        return $this[$name];
    }

    public function has($name)
    {
        return isset($this[$name]);
    }

    /**
     * Set a value to true on the flag name.
     * Will return false if the flag was already present and true if it was not there.
     *
     * @param $name
     * @return bool
     */
    public function flag($name)
    {
        $result = !$this->has($name);
        $this->set($name, true);
        return $result;
    }

    public function __toString()
    {
        return "";
    }
} 