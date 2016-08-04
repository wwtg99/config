<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/8/4
 * Time: 17:10
 */

namespace Wwtg99\Config\Source;


use Predis\Client;
use Wwtg99\Config\Common\ArrayUtils;

class RedisSource extends AbstractSource
{

    /**
     * @var string
     */
    protected $key = 'config';

    /**
     * @var Client
     */
    protected $redis = null;

    /**
     * RedisSource constructor.
     * @param string $key
     * @param $parameters
     * @param $options
     */
    public function __construct($key = 'config', $parameters = null, $options = null)
    {
        if ($key) {
            $this->key = $key;
        }
        $this->redis = new Client($parameters, $options);
    }


    /**
     * @return mixed
     */
    public function load()
    {
        return [];
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        if (ArrayUtils::hasInArray($this->conf, $name)) {
            return ArrayUtils::getInArray($this->conf, $name);
        } else {
            if ($this->redis) {
                $f = $this->getField($name);
                $v = $this->redis->hget($this->key, $f);
                if (!is_null($v)) {
                    $v = unserialize($v);
                    $this->conf[$f] = $v;
                    return ArrayUtils::getInArray($this->conf, $name);
                }
            }
        }
        return null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        if (ArrayUtils::hasInArray($this->conf, $name)) {
            return true;
        } else {
            if ($this->redis) {
                $f = $this->getField($name);
                $b = $this->redis->hexists($this->key, $f);
                if ($b) {
                    $v = $this->redis->hget($this->key, $f);
                    $this->conf[$f] = unserialize($v);
                    return ArrayUtils::hasInArray($this->conf, $name);
                }
            }
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function export()
    {
        if ($this->redis) {
            $keys = $this->redis->hkeys($this->key);
            foreach ($keys as $key) {
                $v = $this->redis->hget($this->key, $key);
                $v = unserialize($v);
                $this->conf[$key] = $v;
            }
        }
        return parent::export();
    }

    /**
     * Dump config array to redis.
     *
     * @param array $conf
     * @return $this
     */
    public function dumpConfig($conf = null)
    {
        if (is_null($conf)) {
            $conf = $this->conf;
        }
        if (is_array($conf) && $this->redis) {
            foreach ($conf as $k => $value) {
                $this->redis->hset($this->key, $k, serialize($value));
            }
        }
        return $this;
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getField($name)
    {
        $dot = strpos($name, '.');
        if ($dot > 0) {
            return substr($name, 0, $dot);
        }
        return $name;
    }

}