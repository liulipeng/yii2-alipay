<?php
/**
 * Created by PhpStorm.
 * User: liulipeng
 * Date: 16/7/3
 * Time: 下午5:42
 */

namespace izyue\alipay;


use yii;

class AlipayConfig
{

    private $_alipayConfig = [];

    /**
     * AlipayConfig constructor.
     */
    public function __construct() {
        //↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        //合作身份者id，以2088开头的16位纯数字
        $this->_alipayConfig['partner'] = Yii::$app->params['alipayPartner'];
        //收款支付宝账号
        $this->_alipayConfig['seller_email'] = Yii::$app->params['alipaySellerEmail'];
        //安全检验码，以数字和字母组成的32位字符
        $this->_alipayConfig['key'] = Yii::$app->params['alipayKey'];
        //↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
        //签名方式 不需修改
        $this->_alipayConfig['sign_type'] = strtoupper('MD5');
        //字符编码格式 目前支持 gbk 或 utf-8
        $this->_alipayConfig['input_charset'] = strtolower('utf-8');
        //ca证书路径地址，用于curl中ssl校验
        //请保证cacert.pem文件在当前文件夹目录中
        $this->_alipayConfig['cacert'] = __DIR__ . '/cacert.pem';
        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $this->_alipayConfig['transport'] = 'http';
    }

    /**
     * @param $name
     * @param $value
     * @return void
     */
    public function setAlipayConfig($name, $value) {
        $this->_alipayConfig[$name] = $value;
    }

    /**
     * @return array
     */
    public function getAlipayConfig() {
        return $this->_alipayConfig;
    }

}