<?php
namespace apiProcess\sdk\encrypt;
abstract class Driver
{
    /**
     * 加密
     * @param string $encrypt
     * @return String
     */
    abstract public function encrypt(string $encrypt):String;

    /**
     * 解密
     * @param string $encrypt
     * @return String
     */
    abstract public function decrypt(string $decrypt):String;
}
