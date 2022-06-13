<?php
namespace apiProcess\sdk;
use apiProcess\sdk\utils\Utils;

/**
 * 接口请求对象的抽象类
 * Class AbstractRequestBody
 * @package apiProcess\sdk
 */
abstract class AbstractRequestBody
{
    /**
     * 因为每个接口的路径不同，所以需要每个接口都显示的指定请求路径
     * @var string $path
     */
    protected $path;

    /**
     * 我们把每个接口的业务参数最后统一收纳在data数组中
     * @var array $data
     */
    protected $data = [];

    /**
     * 配置参数
     * @var
     */
    protected $config = [];

    /**
     * 设置请求需要的app_id
     * @var
     */
    protected $app_id = null;

    /**
     * 设置请求需要的app_secret
     * @var
     */
    protected $app_secret = null;

    /**
     * 业务请求的方法
     * @var
     */
    protected $action;

    /**
     * AbstractRequestBody constructor.
     */
    public function __construct(){}

    /**
     * @return string
     * 最后发起请求的时候获取接口路径
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * 抽象化对象，强制要求每个接口类必须设定接口路径
     * @return mixed
     */
    abstract protected function setPath(string $path);

    /**
     * 抽象化对象，强制要求每个接口类必须设定请求方法
     * @return mixed
     */
    abstract protected function setAction(string $action);

    /**
     * 配置参数校验
     * @return mixed
     */
    abstract public function validate();

    /**
     * 打包公共参数
     * @param array $option
     * @return array
     */
    public function package(array $option):array
    {
        $data = $this->getData();
        $params['data'] = json_encode($data);
        $params['version'] = Constants::VERSION;
        //$params['nonce_str'] = Utils::getNonceStr();
        $params['app_id'] = $this->getAppId();
        $params['timestamp'] = time();
        $params['action'] = $this->getAction();
        if (!$option['is_open_encrypt']) {
            $params['app_sign'] = Utils::sign($data,$this->getAppSecret());
        }
        return $params;
    }

    protected function getData()
    {
        return $this->data;
    }

    protected function getAction()
    {
        return $this->action;
    }

    public function getAppId()
    {
        return $this->app_id;
    }

    public function getAppSecret()
    {
        return $this->app_secret;
    }

    public function getConfig()
    {
        return $this->config;
    }
}
