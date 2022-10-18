<?php
namespace apiProcess\sdk\encrypt\driver;
use apiProcess\sdk\exception\EncryptException;

class Rsa
{
    protected $publicKeyFile;
    protected $privateKeyFile;
    protected $password;

    public function __construct(array $config)
    {
        $this->publicKeyFile =  $this->fixKeyArgument($config['rsa_public_key']??'');
        $this->privateKeyFile = $this->fixKeyArgument($config['rsa_private_key']??'');
        $this->password = $config['rsa_password']??null;
    }

    public function fixKeyArgument($keyFile)
    {
        if (strpos($keyFile, '/') === 0) {
            // This looks like a path, let us prepend the file scheme
            return 'file://' . $keyFile;
        }

        return $keyFile;
    }


    /**
     * 设置要在加密和解密期间使用的密码
     * @param $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * 公钥加密
     * @param $data
     * @return string|null
     * @throws EncryptException
     */
    public function encrypt($data)
    {
        if (!is_string($data)) {
            return null;
        }

        // 加载public key
        $publicKey = openssl_pkey_get_public($this->publicKeyFile);

        if (!$publicKey) {
            throw new EncryptException("OpenSSL: 无法获取公共密钥进行加密");
        }

        $encrypted = '';
        $key_len = $this->_getKenLen();
        $part_len = $key_len / 8 - 11;
        $parts = str_split($data, $part_len);

        foreach ($parts as $part) {
            $encrypted_temp = '';
            openssl_public_encrypt($part, $encrypted_temp, $publicKey);
            $encrypted .= $encrypted_temp;
        }

        // 释放密钥资源
        openssl_free_key($publicKey);

        return base64_encode($encrypted);
    }


    /**
     * 私钥解密
     * @param $data
     * @return string|null
     * @throws EncryptException
     */
    public function decrypt($data)
    {
        if (!is_string($data)) {
            return null;
        }

        if ($this->privateKeyFile === null) {
            throw new EncryptException("无法解密：未提供私钥");
        }

        $privateKey = openssl_pkey_get_private($this->privateKeyFile, $this->password);

        if (!$privateKey) {
            throw new EncryptException("OpenSSL: 无法获取私钥进行解密");
        }

        $decrypted = "";
        $key_len = $this->_getKenLen();
        $part_len = $key_len / 8;
        $base64_decoded = base64_decode($data);
        $parts = str_split($base64_decoded, $part_len);

        foreach ($parts as $part) {
            $decrypted_temp = '';
            $bool = openssl_private_decrypt($part, $decrypted_temp,$privateKey);
            if (!$bool) throw new EncryptException("OpenSSL: 解密失败");
            $decrypted .= $decrypted_temp;
        }

        return $decrypted;
    }

    /**
     * 获取公钥位数
     * @return mixed
     */
    private function _getKenLen()
    {
        $pub_id = openssl_get_publickey($this->publicKeyFile);
        return openssl_pkey_get_details($pub_id)['bits'];
    }
}
