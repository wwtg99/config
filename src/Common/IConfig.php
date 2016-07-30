<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/7/25 0025
 * Time: 下午 9:26
 */

namespace Wwtg99\Config\Common;


interface IConfig
{

    /**
     * Get config by name, dot(.) supported to search in array. $defval will be returned if name is not exists.
     *
     * @param string $name
     * @param $defval
     * @return mixed
     */
    public function get($name, $defval = null);

    /**
     * Set config, dot(.) supported to set in array.
     *
     * @param string $name
     * @param $value
     * @return mixed
     */
    public function set($name, $value);

    /**
     * Check if name exists, dot(.) supported to search in array.
     *
     * @param string $name
     * @return bool
     */
    public function has($name);

    /**
     * @param array $conf
     * @return IConfig
     */
    public function import(array $conf);

    /**
     * Export config.
     *
     * @return mixed
     */
    public function export();

    /**
     * Add config source.
     *
     * @param $source
     * @return IConfig
     */
    public function addSource($source);

    /**
     * Load config from cache or sources.
     *
     * @return IConfig
     */
    public function load();
}