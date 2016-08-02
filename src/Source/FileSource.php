<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/7/28 0028
 * Time: 下午 9:12
 */

namespace Wwtg99\Config\Source;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Wwtg99\Config\Common\ArrayUtils;
use Wwtg99\Config\Source\Loader\JsonLoader;
use Wwtg99\Config\Source\Loader\PHPLoader;

class FileSource extends AbstractSource
{

    /**
     * @var array
     */
    protected $confFiles;

    /**
     * @var array
     */
    protected $loaders = [];

    /**
     * FileSource constructor.
     * @param array|string $confDirs
     * @param array|string $files
     */
    public function __construct($confDirs, $files)
    {
        $locator = new FileLocator($confDirs);
        $fs = [];
        if (is_array($files)) {
            foreach ($files as $file) {
                $f = $locator->locate($file, null, false);
                if (is_array($f)) {
                    $fs = array_merge($fs, $f);
                } else {
                    $fs = array_push($fs, $f);
                }
            }
        } else {
            $fs = $locator->locate($files, null, false);
        }
        if (is_array($fs)) {
            $this->confFiles = array_unique($fs);
        } else {
            $this->confFiles = [$fs];
        }
    }

    /**
     * @return mixed
     */
    public function load()
    {
        $rev = new LoaderResolver($this->loaders);
        $dloader = new DelegatingLoader($rev);
        foreach ($this->confFiles as $confFile) {
            $conf = $dloader->load($confFile);
            $this->conf = array_merge($this->conf, $conf);
        }
        return $this->conf;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        return ArrayUtils::getInArray($this->conf, $name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return ArrayUtils::hasInArray($this->conf, $name);
    }

    /**
     * @param LoaderInterface $loader
     * @return $this
     */
    public function addLoader($loader)
    {
        if ($loader instanceof LoaderInterface) {
            array_push($this->loaders, $loader);
        }
        return $this;
    }

}