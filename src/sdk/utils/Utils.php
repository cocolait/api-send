<?php
namespace apiProcess\sdk\utils;
class Utils
{
    /**
     * 获取随机字符串
     * @param int $length
     */
    public static function getNonceStr(){}

    /**
     * 签名算法
     * @param array $params
     * @return string
     */
    public static function sign(array $params,string $appSecret):string
    {
        if (empty($params) || !$appSecret) {
            return '';
        }
        ksort($params);
        reset($params);
        $filter = [];
        if (count($params) != count($params, 1)) {
            // 多维情况 批量取最后一组
            $popEnd = array_pop($params);
            $params = array_pop($popEnd);
        }
        foreach ($params as $k => $v) {
            if (trim($v) !== '') {
                $filter[$k] = $v;
            }
        }
        $params = http_build_query($filter);
        $sign = strtoupper(md5(trim($appSecret). $params. trim($appSecret)));
        return $sign;
    }

    /**
     * 验证签名
     * @param array $params 参数
     * @param string $app_secret 秘钥
     * @return bool
     */
    public static function verifySign(array $params,string $app_secret):bool
    {
        // 验证参数中是否有签名
        if (!$params || !isset($params['timestamp'],$params['app_sign'])) {
            return false;
        }
        // 验证请求， 10分钟失效
        if (time() - $params['timestamp'] > 600) {
            return false;
        }
        $app_sign = $params['app_sign'];
        unset($params['app_sign'],$params['timestamp']);
        $current_sign = self::sign($params,$app_secret);
        if ($app_sign == $current_sign) {
            return true;
        }
        return false;
    }
}
