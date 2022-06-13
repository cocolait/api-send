<?php
namespace apiProcess\sdk;
use apiProcess\sdk\utils\Loader;
/**
 * @mixin \apiProcess\sdk\encrypt\Driver\Aes
 */
class Encrypt
{
    /**
     * 缓存实例
     * @var array
     */
    protected $instance = [];

    protected static $init = null;

    /**
     * 配置
     * @var array
     */
    protected $config = [
        'aes_secret_key' => '',
        'aes_iv' => '',
        'aes_options' => 0,
        'aes_method' => 'AES-128-ECB',
        'rsa_public_key' => '',
        'rsa_private_key' => '',
        'rea_password' => '',
        'type' => 'aes'
    ];

    protected function __construct(array $config = [])
    {
        $this->setConfig($config);
        $this->create();
    }

    public static function init(array $config)
    {
        if (is_null(self::$init)) {
            self::$init = new static($config);
        }
        return self::$init;
    }

    protected function create()
    {
        $name = $this->config['type']??'aes';
        if (!isset($this->instance[$name])) {
            $type = !empty($this->config['type']) ? $this->config['type'] : 'Aes';
            $this->instance[$name] = Loader::factory($type,'\\apiProcess\\sdk\\encrypt\\driver\\', $this->config);
        }
        return $this->instance[$name];
    }

    public function setConfig(array $config)
    {
        $this->config = array_merge($this->config, $config);
        return $this;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->create(), $method], $args);
    }
}
