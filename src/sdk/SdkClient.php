<?php
namespace apiProcess\sdk;
use apiProcess\sdk\utils\Http;
use think\Exception;
use apiProcess\sdk\config\Config;
/**
 * Class SdkClient
 */
class SdkClient
{
    /**
     * 配置参数 详细看README
     * @var array
     */
    protected $option = [];

    /**
     * 操作句柄
     * @var object
     */
    protected $rsaHandler;

    /**
     * 操作句柄
     * @var object
     */
    protected $aesHandler;


    protected $aesKey;

    // 初始化
    protected function __construct()
    {
        $this->option = $this->getConfig();
    }

    /**
     * 发送数据
     * @param AbstractRequestBody $requestBody
     * @return array|string
     * @throws Exception
     */
    protected function send(AbstractRequestBody $requestBody)
    {
        try {
            // 校验参数
            $requestBody->validate();
            // 获取最新的配置
            $config = $requestBody->getConfig();
            $this->aesKey = uniqid('hs_');
            // 初始化句柄
            $this->rsaHandler = Encrypt::init([
                'type' => 'rsa',
                'rsa_public_key' => $config['rsa_public_key'],
                'rsa_private_key' => $config['rsa_private_key'],
            ]);
            // 拼凑url
            $route = $requestBody->getPath()??$config['request_route'];
            if (empty($route)) {
                throw new \Exception('请配置请求路由参数`request_route`');
            }
            $url = $this->option['domain_prod'] . $route;
            // 获取请求参数
            $data = $requestBody->package($this->option);
            // 判断传输方式
            $headers = [];
            $is_open_encrypt = $this->option['is_open_encrypt']??Config::IS_OPEN_ENCRYPT;
            if ($is_open_encrypt) {
                // 密文方式
                // 先用rsa加密出aes秘钥
                $aes_key = $this->rsaHandler->encrypt($this->aesKey);
                $this->aesHandler = Encrypt::init([
                    'type' => 'aes',
                    'aes_secret_key' => $this->aesKey,
                    'aes_method' => 'AES-128-CBC'
                ]);
                $data = $this->aesHandler->encrypt(json_encode($data));
                $request_param['content'] = $data;
                // 生成动态密钥
                array_push($headers,"app-encrypt: {$aes_key}","app-id: {$requestBody->getAppId()}");
            } else {
                // 明文方式
                $request_param = $data;
            }
            $request_type = $config['request_type']??Config::REQUEST_TYPE;
            if (!in_array($request_type,['get','post'])) {
                throw new Exception('不支持请求类型：'.$request_type);
            }
            $response = Http::$request_type($url,$request_param,$headers);
        } catch (\Exception $exception) {
            throw new Exception($exception->getMessage());
        }
        return $response;
    }

    /**
     * 解密数据
     * @param string $data 数据秘钥
     * @param string $aes_key aes动态秘钥 就是send方法 头部app-encrypt 发送的值
     * @param array $config
     * @return mixed|string
     * @throws Exception
     */
    protected function decrypt(string $data,string $aes_key, array $config)
    {
        if (!$data) throw new Exception('缺少content加密值');
        $write_log = json_encode(['data' => $data,'aes_key' => $aes_key]);
        $this->rsaHandler = Encrypt::init([
            'type' => 'rsa',
            'rsa_public_key' => $config['rsa_public_key'],
            'rsa_private_key' => $config['rsa_private_key'],
        ]);
        try {
            $aes_key = $this->rsaHandler->decrypt($aes_key);
        } catch (\Exception $e) {
            throw new Exception('rsa解密数据失败-###错误:' . $e->getMessage() . '##源数据:' . $write_log);
        }
        try {
            $this->aesHandler = Encrypt::init([
                'type' => 'aes',
                'aes_secret_key' => $aes_key,
                'aes_method' => 'AES-128-CBC'
            ]);
            $data = $this->aesHandler->decrypt($data);
            if (!$data)  throw new Exception('aes解密数据失败' . '##源数据:' . $write_log);
        } catch (\Exception $e) {
            throw new Exception('aes解密数据失败-###错误:' . $e->getMessage() . '##源数据:' . $write_log);
        }
        $data = json_decode($data,1);
        if (is_null($data)) throw new Exception('解密数据格式错误' . '##源数据:' . $write_log);
        return $data;
    }

    /**
     * 对返回数据统一处理，数据过滤
     * 比如转换成数组，还是对象，如果是对象可以new Response，在构造函数处理
     * @param string $content
     * @return string
     */
   /* private function response(string $content)
    {
        return $content;
    }*/

    /**
     * 获取原有的配置文件
     * @return array
     * @throws \ReflectionException
     */
    protected function getConfig()
    {
        $r = new \ReflectionClass((new Config()));
        return array_change_key_case($r->getConstants(),CASE_LOWER);
    }
}
