<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/7/25 0025
 * Time: 下午 9:38
 */

namespace Wwtg99\Config\Common;


use Wwtg99\Config\Source\IConfigSource;

class ConfigPool implements IConfig
{

    /**
     * @var array
     */
    protected $conf = [];

    /**
     * @var array
     */
    protected $sources = [];

    /**
     * @var string
     */
    protected $cache;

    /**
     * @var bool
     */
    protected $useCache = false;

    /**
     * ConfigPool constructor.
     * @param $cache
     */
    public function __construct($cache = null)
    {
        $this->cache = $cache;
    }

    /**
     * @param string $name
     * @param $defval
     * @return mixed
     */
    public function get($name, $defval = null)
    {
        if ($this->useCache) {
            return ArrayUtils::getInArray($this->conf, $name, $defval);
        } else {
            $re = ArrayUtils::hasInArray($this->conf, $name);
            if ($re) {
                return ArrayUtils::getInArray($this->conf, $name, $defval);
            } else {
                foreach ($this->sources as $source) {
                    if ($source instanceof IConfigSource) {
                        $re = $source->get($name);
                        if (!is_null($re)) {
                            $this->set($name, $re);
                            return $re;
                        }
                    }
                }
            }
        }
        return $defval;
    }

    /**
     * @param string $name
     * @param $value
     * @return mixed
     */
    public function set($name, $value)
    {
        $this->conf = ArrayUtils::setInArray($this->conf, $name, $value);
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        $re = ArrayUtils::hasInArray($this->conf, $name);
        if (!$re && !$this->useCache) {
            foreach ($this->sources as $source) {
                if ($source instanceof IConfigSource) {
                    $re = $source->has($name);
                    if ($re === true) {
                        return true;
                    }
                }
            }
        }
        return $re;
    }

    /**
     * @param array $conf
     * @return IConfig
     */
    public function import(array $conf)
    {
        $this->conf = array_merge($this->conf, $conf);
        return $this;
    }

    /**
     * @return mixed
     */
    public function export()
    {
        return $this->conf;
    }

    /**
     * @param IConfigSource|array $source
     * @return IConfig
     */
    public function addSource($source)
    {
        if (is_array($source)) {
            $this->sources = array_merge($this->sources, $source);
        } else {
            array_push($this->sources, $source);
        }
        return $this;
    }

    /**
     * Load config from cache or sources.
     *
     * @return IConfig
     */
    public function load()
    {
        $this->conf = $this->loadCache();
        if ($this->conf) {
            $this->useCache = true;
        } else {
            foreach ($this->sources as $source) {
                if ($source instanceof IConfigSource) {
                    $conf = $source->load();
                    if (is_array($conf)) {
                        $this->import($conf);
                    }
                }
            }
            $this->saveCache();
        }
        return $this;
    }

    /**
     * Save conf to cache file.
     */
    public function saveCache()
    {
        if ($this->cache) {
            $content = $this->export();
            file_put_contents($this->cache, serialize($content));
        }
    }

    /**
     * Load conf from cache file.
     * @return array
     */
    public function loadCache()
    {
        $f = $this->cache;
        if (file_exists($f)) {
            $content = file_get_contents($f);
            return unserialize($content);
        }
        return [];
    }

}