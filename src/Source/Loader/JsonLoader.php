<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/7/28 0028
 * Time: 下午 9:52
 */

namespace Wwtg99\Config\Source\Loader;


use Symfony\Component\Config\Loader\Loader;

class JsonLoader extends Loader
{

    /**
     * Loads a resource.
     *
     * @param mixed $resource The resource
     * @param string|null $type The resource type or null if unknown
     * @return array
     *
     * @throws \Exception If something went wrong
     */
    public function load($resource, $type = null)
    {
        $conf = json_decode(file_get_contents($resource), true);
        return $conf;
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
        return is_string($resource) && 'json' === strtolower(pathinfo($resource, PATHINFO_EXTENSION));
    }
}