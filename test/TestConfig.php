<?php

/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/7/30 0030
 * Time: 上午 11:50
 */
class TestConfig extends PHPUnit_Framework_TestCase
{
    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass()
    {
        date_default_timezone_set('Asia/Shanghai');
        require '../vendor/autoload.php';
    }

    public function testArrayUtils()
    {
        $arr = [
            'a'=>'val1',
            'b'=>['bb1'=>'val2', 'bb2'=>['bbb1'=>'val3']],
            'c'=>['a', 'b', 'c'],
            'd'=>true,
            'e'=>['ee1'=>null, 'ee2'=>[1, 2]]
        ];
        $tests1 = [
            'a'=>true,
            'b'=>true,
            'b.bb1'=>true,
            'b.bb2.bbb1'=>true,
            'b.bb3'=>false,
            'c'=>true,
            'c.0'=>true,
            'c.3'=>false,
            'd'=>true,
            'd.a'=>false,
            'e'=>true,
            'e.ee1'=>true,
            'e.ee2.0'=>true,
            'e.ee3'=>false,
            '.e'=>false,
            'e.'=>false,
        ];
        foreach ($tests1 as $t => $exp) {
            $this->assertEquals($exp, \Wwtg99\Config\Common\ArrayUtils::hasInArray($arr, $t), "Test $t");
        }
        $tests2 = [
            'a'=>'val1',
            'b.bb1'=>'val2',
            'b.bb2.bbb1'=>'val3',
            'b.bb3'=>null,
            'c'=>['a', 'b', 'c'],
            'c.1'=>'b',
            'd'=>true,
            'd.a'=>null,
            'e.ee1'=>null,
            'e.ee2'=>[1, 2],
            'e.ee2.0'=>1,
            'e.ee3'=>null,
            '.e'=>null,
            'e.'=>null,
        ];
        foreach ($tests2 as $t => $exp) {
            $this->assertEquals($exp, \Wwtg99\Config\Common\ArrayUtils::getInArray($arr, $t), "Test $t");
        }
        $this->assertEquals('aa', \Wwtg99\Config\Common\ArrayUtils::getInArray($arr, 'f', 'aa'), "Test default value");
        $this->assertEquals(null, \Wwtg99\Config\Common\ArrayUtils::getInArray($arr, 'e.ee1', 'aa'), "Test default value");
        $arr = [];
        $this->assertEquals(['a'=>'val1'], \Wwtg99\Config\Common\ArrayUtils::setInArray($arr, 'a', 'val1'));
        $this->assertEquals(['a'=>'val1'], \Wwtg99\Config\Common\ArrayUtils::setInArray($arr, 'a.aa', 'val2'));
        $this->assertEquals(['a'=>'val1', 'b'=>['bb'=>'val2']], \Wwtg99\Config\Common\ArrayUtils::setInArray($arr, 'b.bb', 'val2'));
        $this->assertEquals(['a'=>'val1', 'b'=>['bb'=>'val2', 'bbb'=>[1, 2]]], \Wwtg99\Config\Common\ArrayUtils::setInArray($arr, 'b.bbb', [1, 2]));
        $this->assertEquals(['a'=>'val1', 'b'=>['bb'=>'val2', 'bbb'=>[1, 2, 3]]], \Wwtg99\Config\Common\ArrayUtils::setInArray($arr, 'b.bbb.2', 3));
        $this->assertEquals(['a'=>'val1', 'b'=>['bb'=>'val2', 'bbb'=>[1, 2, 3]], 'c'=>null], \Wwtg99\Config\Common\ArrayUtils::setInArray($arr, 'c', null));
    }

    public function testFileSource()
    {
        $source = new \Wwtg99\Config\Source\FileSource(__DIR__, ['conf1.json', 'conf2.php', 'conf3.yml']);
        $source->addLoader(new \Wwtg99\Config\Source\Loader\JsonLoader())->addLoader(new \Wwtg99\Config\Source\Loader\PHPLoader())->addLoader(new \Wwtg99\Config\Source\Loader\YamlLoader());
        $source->load();
        $tests1 = [
            'a'=>true,
            'b'=>true,
            'b.bb'=>true,
            'b.bb.0'=>true,
            'b.bb.3'=>false,
            'b.bbb'=>true,
            'b.bbbb'=>false,
            'c'=>true,
            'c.ccc'=>true,
            'c.'=>false,
            'd'=>true,
            'd.d1'=>true,
            'd.d2.1'=>true,
            'd.d3'=>false,
        ];
        foreach ($tests1 as $t => $exp) {
            $this->assertEquals($exp, $source->has($t), "Test $t");
        }
        $tests2 = [
            'a'=>'val2',
            'b.bb'=>[1, 2],
            'b.bbb'=>true,
            'c.cc.1'=>3,
            'c.ccc'=>null,
            'd.d1'=>'val3',
            'd.d2.0'=>'a',
        ];
        foreach ($tests2 as $t => $exp) {
            $this->assertEquals($exp, $source->get($t), "Test $t");
        }
        $this->assertEquals([
            'a'=>'val2',
            'b'=>['bb'=>[1,2], 'bbb'=>true],
            'c'=>['cc'=>[1,3,5], 'ccc'=>null],
            'd'=>['d1'=>'val3', 'd2'=>['a', 'b']]
        ], $source->export());
    }

    public function testConfigPool()
    {
        $conf = new \Wwtg99\Config\Common\ConfigPool();
        $source = new \Wwtg99\Config\Source\FileSource(__DIR__, 'conf1.json');
        $source->addLoader(new \Wwtg99\Config\Source\Loader\JsonLoader());
        $conf->addSource($source);
        $conf->load();
        $arr = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'conf1.json'), true);
        $this->assertEquals($arr, $conf->export());
        $this->assertEquals(true, $conf->has('a'));
        $this->assertEquals('val1', $conf->get('a'));
        $this->assertEquals(2, $conf->get('b.bb.1'));
        $this->assertEquals('aa', $conf->get('b.bb.2', 'aa'));
        $conf->set('c.cc', true);
        $arr['c'] = ['cc'=>true];
        $this->assertEquals($arr, $conf->export());
    }

    public function testCache()
    {
        $cache = __DIR__ . DIRECTORY_SEPARATOR . 'test.cache';
        if (file_exists($cache)) {
            unlink($cache);
        }
        $conf = new \Wwtg99\Config\Common\ConfigPool($cache);
        $source = new \Wwtg99\Config\Source\FileSource(__DIR__, 'conf2.php');
        $source->addLoader(new \Wwtg99\Config\Source\Loader\PHPLoader());
        $conf->addSource($source);
        $conf->load();
        $this->assertEquals('val2', $conf->get('a'));
        $conf->set('a', 'aaa');
        $conf->saveCache();
        $conf = new \Wwtg99\Config\Common\ConfigPool($cache);
        $source = new \Wwtg99\Config\Source\FileSource(__DIR__, 'conf2.php');
        $source->addLoader(new \Wwtg99\Config\Source\Loader\PHPLoader());
        $conf->addSource($source);
        $conf->load();
        $this->assertEquals('aaa', $conf->get('a'));
    }
}
