需求：
----
 ```
 PHP 接口数据加密传输
 服务端和客户端 分别都是php
 ```

sdk配置参数介绍：
 ----
 ```
data:{
   "domain_prod": "http://hs.com", --请求域名
   "app_id": "dp30ig6w4j270lr6911qgheqvaocgf8p", --颁发的app_id
   "app_secret": "0192023a7bbd73250516f069df18b500", --颁发的app_secret
   "rsa_public_key": "-----BEGIN PUBLIC KEY-----\r\nMFwEAAQ==\r\n-----END PUBLIC KEY-----", --rsa公钥
   "rsa_private_key": "-----BEGIN PRIVATE KEY-----\r\n0HkPk1wgwp\r\n-----END PRIVATE KEY-----", --rsa私钥
   "is_open_encrypt": true,  --是否开启加密传输
   "request_type": 'post', --选填 默认请求方式
   "request_route": '/api/test', --选填 请求路由
}
```

加密传输流程：
----
 ```
1.服务端发送给客户端数据时，使用ras加密出动态的aes密钥,对数据进行aes加密，并且将动态aes秘钥放置头部
2.客户端接受到数据时进行验签，拿到头部传过来的动态密钥，先进行RSA解密,拿到解密后的AES_key密钥在进行AES数据解密
3.简单理解就是，rsa加密动态的密钥放置头部,拿到头部的密钥，先RSA解密出AES密钥，再拿解密后的aes_key进行二次解密出真实的数据
```
