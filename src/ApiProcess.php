<?php
namespace apiProcess;
use apiProcess\sdk\config\Config;
use apiProcess\sdk\SdkClient;
use apiProcess\sdk\OneApi;
/**
 * 入口文件
 * Class ApiProcess
 * @package ApiProcess
 */
class ApiProcess extends SdkClient
{
    protected static $instance;
    protected static $apiRequest;
    protected function __construct(array $option = [])
    {
        parent::__construct();
        $this->option = array_merge($this->option, $option);
        if (is_null(self::$apiRequest)) {
            self::$apiRequest = new OneApi($this->option);
        }
    }

    /**
     * 初始化
     * @param array $option
     * @return static
     */
    public static function init(array $option = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($option);
        }
        return self::$instance;
    }

    /**
     * 推送数据
     * @param string $action 方法名
     * @param array $data 数据
     * @param array $request_route 请求的路由地址
     * @return array|string
     * @throws \think\Exception
     */
    public function push(string $action, array $data, string $request_route = '')
    {
        $request = self::$apiRequest
            ->setAction($action)
            ->setPath($request_route??Config::REQUEST_ROUTE)
            ->setData($data);
        return $this->send($request);
    }

    /**
     * 解密请求过来的数据
     * @param string $data 数据秘钥
     * @param string $aes_key aes动态秘钥
     * @return mixed|string
     * @throws \think\Exception
     */
    public function requestDecrypt(string $data,string $aes_key)
    {
        return $this->decrypt($data,$aes_key,$this->option);
    }

    // 禁止外部克隆
    protected function __clone(){}
}
