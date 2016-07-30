<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/7/25 0025
 * Time: 下午 9:37
 */

namespace Wwtg99\Config\Source;


interface IConfigSource
{

    /**
     * @return mixed
     */
    public function load();

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name);

    /**
     * @param string $name
     * @return bool
     */
    public function has($name);

    /**
     * @return mixed
     */
    public function export();
}