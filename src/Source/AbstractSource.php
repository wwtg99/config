<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/7/28 0028
 * Time: 下午 9:14
 */

namespace Wwtg99\Config\Source;


abstract class AbstractSource implements IConfigSource
{

    /**
     * @var array
     */
    protected $conf = [];

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        return isset($this->conf[$name]) ? $this->conf[$name] : null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->conf[$name]);
    }

    /**
     * @return mixed
     */
    public function export()
    {
        return $this->conf;
    }


}