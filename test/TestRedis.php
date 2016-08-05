<?php

/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/8/4
 * Time: 17:49
 */
class TestRedis extends PHPUnit_Framework_TestCase
{

    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass()
    {
        date_default_timezone_set('Asia/Shanghai');
        require '../vendor/autoload.php';
    }

    public function testRedisSource()
    {
        $param = ['host'=>'192.168.0.21'];
        $conf = [
            'a'=>'v1',
            'b'=>[1, 2, 3],
            'c'=>['a', 'b'],
            'd'=>[
                'd1'=>'v2',
                'd2'=>'v3'
            ],
            'e'=>true,
            'f'=>[
                'v4',
                'f1'=>'v5',
                'f2'=>['ff2'=>'v6'],
                'f3'=>null
            ]
        ];
        $rs = new \Wwtg99\Config\Source\RedisSource('config', $param);
        $rs->dumpConfig($conf);
        $test_has = [
            'a'=>true, 'a.a'=>false, 'b'=>true, 'b.0'=>true, 'b.3'=>false, 'b.b'=>false, 'c.0'=>true, 'd.d1'=>true,
            'd.d3'=>false, 'd.d1.d2'=>false, 'e'=>true, 'f.0'=>true, 'f.f1'=>true, 'f.f2'=>true, 'f.f2.ff2'=>true,
            'f.f2.ff3'=>false, 'f.f3'=>true, 'f.f4'=>false
        ];
        foreach ($test_has as $k => $exp) {
            $this->assertEquals($exp, $rs->has($k), "Test $k");
        }
        $test_get = [
            'a'=>'v1', 'a.a'=>null, 'b'=>[1, 2, 3], 'b.0'=>1, 'b.3'=>null, 'b.b'=>null, 'c.0'=>'a', 'd.d1'=>'v2',
            'd.d3'=>null, 'd.d1.d2'=>null, 'e'=>true, 'f.0'=>'v4', 'f.f1'=>'v5', 'f.f2'=>['ff2'=>'v6'], 'f.f2.ff2'=>'v6',
            'f.f2.ff3'=>null, '.f.f3'=>null, 'f.f4'=>null
        ];
        foreach ($test_get as $k => $exp) {
            $this->assertEquals($exp, $rs->get($k), "Test $k");
        }
        $this->assertEquals($conf, $rs->export());
    }

    /**
     * @depends testRedisSource
     */
    public function testCache()
    {
        $param = ['host'=>'192.168.0.21'];
        $conf = new \Wwtg99\Config\Common\ConfigPool();
        $source = new \Wwtg99\Config\Source\RedisSource('config', $param);
        $conf->addSource($source);
        $conf->load();
        $this->assertEquals('v1', $conf->get('a'));
        $this->assertEquals('v2', $conf->get('d.d1'));
    }
}
