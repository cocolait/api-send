<?php
namespace apiProcess\sdk\config;
class Config
{
    /**
     * 请求域名
     */
    const DOMAIN_PROD = '';

    /**
     * 颁发的app_id
     */
    const APP_ID = '';

    /**
     * 颁发的app_secret
     */
    const APP_SECRET = '';

    /**
     * rsa公钥文件
     */
    const RSA_PUBLIC_KEY = '';

    /**
     * rsa私钥文件
     */
    const RSA_PRIVATE_KEY = '';

    /**
     * 是否开启加密传输
     */
    const IS_OPEN_ENCRYPT = true;

    /**
     * 默认的请求类型
     */
    const REQUEST_TYPE = 'post';

    /**
     * 默认的路由地址
     */
    const REQUEST_ROUTE = '';
}
