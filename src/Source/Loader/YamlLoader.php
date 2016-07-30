<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/7/30 0030
 * Time: 上午 10:41
 */

namespace Wwtg99\Config\Source\Loader;


use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Yaml\Yaml;

class YamlLoader extends Loader
{
    /**
     * Loads a resource.
     *
     * @param mixed $resource The resource
     * @param string|null $type The resource type or null if unknown
     * @return mixed
     *
     * @throws \Exception If something went wrong
     */
    public function load($resource, $type = null)
    {
        return Yaml::parse(file_get_contents($resource));
    }

    /**
     * Returns whether this class supports the given resource.
     *
     * @param mixed $resource A resource
     * @param string|null $type The resource type or null if unknown
     *
     * @return bool True if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yml' === strtolower(pathinfo($resource, PATHINFO_EXTENSION));
    }

}