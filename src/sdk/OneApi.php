<?php
namespace apiProcess\sdk;
class OneApi extends AbstractRequestBody
{
    public function __construct(array $option)
    {
        parent::__construct();
        $this->app_id = $option['app_id'];
        $this->app_secret = $option['app_secret'];
        $this->config = $option;
    }

    public function setAction($value)
    {
        $this->action = $value;
        return $this;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * 验证配置参数
     * @return mixed|void
     * @throws \Exception
     */
    public function validate()
    {
        $configData = $this->config();
        $keys = array_keys($configData);
        $sendKey = [];
        foreach ($this->config as $key => $value) {
            if (in_array($key,$keys)) {
                $sendKey[] = $key;
                if ($value === '') {
                    throw new \Exception("请配置‘{$key}’" . $configData[$key]);
                }
            }
        }
        $diff_keys = array_diff($keys,$sendKey);
        if ($diff_keys) {
            $errMsg = '';
            foreach ($diff_keys as $v_key) {
                $errMsg .= "请配置‘{$key}’" . $configData[$v_key] . PHP_EOL;
            }
            throw new \Exception($errMsg);
        }
        return true;
    }

    /**
     * 字段转换
     */
    public function transfer(&$response)
    {
        // TODO: Implement transfer() method.
    }

    public function setPath($value)
    {
        $this->path = $value;
        return $this;
    }

    private function config()
    {
        return [
            'domain_prod' => '请求域名',
            'app_id' => 'APP_ID',
            'app_secret' => 'APP_SECRET',
            'rsa_public_key' => 'RSA公钥',
            'rsa_private_key' => 'RSA私钥',
            'is_open_encrypt' => '是否开启加密传输',
        ];
    }
}
