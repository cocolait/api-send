<?php
namespace apiProcess;
class Test
{
    /**
     * sdk调用方式
     * @throws \think\Exception
     */
    public function index()
    {
        // 调用方式：配置调用方式 配置参数字段注释请看 README.md
        $data = [
            'uid' => 10,
            'uri' => '/home/main',
            'referer_id' => 0,
            'referer_uri' => '',
            'ip' => '192.168.1.2',
            'device_type' => 'PC',
            'exit_time' => '2020-08-08 12:30:00',
            'stay_second' => 300,
        ];
        $option = [
            "domain_prod" => "http://hs.com",
            "app_id" => "dp30ig6w4j270lr6911qgheqvaocgf8p",
            "app_secret"=> "0192023a7bbd73250516f069df18b500",
            "rsa_public_key" => "-----BEGIN PUBLIC KEY-----\r\nMFwEAAQ==\r\n-----END PUBLIC KEY-----",
            "rsa_private_key"=> "-----BEGIN PRIVATE KEY-----\r\n0HkPk1wgwp\r\n-----END PRIVATE KEY-----",
            "is_open_encrypt"=> true
        ];
        $request = \apiProcess\ApiProcess::init($option)->push('pushPageAccess',$data);
        //var_dump($request);
    }
}
