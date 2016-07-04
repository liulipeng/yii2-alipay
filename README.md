# Yii2-Alipay

支付宝的Yii2扩展包，支持支付宝所有接口，只需更换对应接口的参数即可！


### Install With Composer

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require izyue/yii2-alipay "*"
```

or for the dev-master

```
php composer.phar require izyue/yii2-alipay "dev"
```

Or, you may add

```
"izyue/yii2-alipay": "*"
```

to the require section of your `composer.json` file and execute `php composer.phar update`.


# Usage

Once the extension is installed, simply modify your application configuration as follows:

``` php
return [
    'alipayPartner' => '2088411622500000',
    'alipaySellerEmail' => 'xxx@xxx.com.cn',
    'alipayKey' => 'j8zjlejjebpgei98cbbgbbmwfr4asdf',
];
```

# AlipayController

这是一个批量付款的栗子，支付宝其它接口换成相应的接口即可使用

``` php
<?php
/**
 * User: liulipeng
 * Date: 16/7/4
 * Time: 上午9:40
 */

namespace backend\controllers\pay;


use yii;
use yii\log\Logger;
use izyue\alipay\AlipayNotify;
use izyue\alipay\AlipayConfig;
use izyue\alipay\AlipaySubmit;
use yii\web\Controller;

class AlipayController extends Controller
{

    public function beforeAction($action)
    {
        if ('notify' == $action->id) {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        /**************************请求参数**************************/

        //服务器异步通知页面路径
        $notify_url = Yii::$app->urlManager->createAbsoluteUrl(['pay/alipay/notify']);
        //需http://格式的完整路径，不允许加?id=123这类自定义参数

        //付款账号
        $email = $_POST['WIDemail'];
        //必填

        //付款账户名
        $account_name = "北京纽斯洛网络科技有限公司";
        //必填，个人支付宝账号是真实姓名公司支付宝账号是公司名称

        //付款当天日期
        $pay_date = date("Y-m-d");
        //必填，格式：年[4位]月[2位]日[2位]，如：20100801

        //批次号
        $batch_no = date("YmdHis");
        //必填，格式：当天日期[8位]+序列号[3至16位]，如：201008010000001

        //付款总金额
        $batch_fee = 0.02;
        //必填，即参数detail_data的值中所有金额的总和

        //付款笔数
        $batch_num = 2;
        //必填，即参数detail_data的值中，“|”字符出现的数量加1，最大支持1000笔（即“|”字符出现的数量999个）

        //付款详细数据
        $detail_data = "流水号1^收款方帐号1^真实姓名^0.01^测试付款1,这是备注|流水号2^收款方帐号2^真实姓名^0.01^测试付款2,这是备注";
        //必填，格式：流水号1^收款方帐号1^真实姓名^付款金额1^备注说明1|流水号2^收款方帐号2^真实姓名^付款金额2^备注说明2....


        /************************************************************/

        $alipayConfig = (new AlipayConfig())->getAlipayConfig();

        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "batch_trans_notify",
            "partner" => trim($alipayConfig['partner']),
            "notify_url"	=> $notify_url,
            "email"	=> trim($alipayConfig['seller_email']),
            "account_name"	=> $account_name,
            "pay_date"	=> $pay_date,
            "batch_no"	=> $batch_no,
            "batch_fee"	=> $batch_fee,
            "batch_num"	=> $batch_num,
            "detail_data"	=> $detail_data,
            "_input_charset"	=> trim(strtolower($alipayConfig['input_charset']))
        );

        //建立请求
        $alipaySubmit = new AlipaySubmit($alipayConfig);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
        return $html_text;
    }

    public function actionNotify()
    {
        $alipayConfig = (new AlipayConfig())->getAlipayConfig();

        Yii::getLogger()->log("alipay Notify Start", Logger::LEVEL_ERROR);

        Yii::getLogger()->log("↓↓↓↓↓↓↓↓↓↓alipayConfig↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓", Logger::LEVEL_ERROR);
        Yii::getLogger()->log(print_r($alipayConfig, true), Logger::LEVEL_ERROR);

        $notify = new AlipayNotify($alipayConfig);
        if ($notify->verifyNotify()) {
            Yii::getLogger()->log('verify Notify success', Logger::LEVEL_ERROR);

            //通知时间  2009-08-12 11:08:32
            $notify_time = Yii::$app->request->post('notify_time');

            //通知类型  batch_trans_notify
            $notifyType = Yii::$app->request->post('notify_type');

            //通知校验ID    70fec0c2730b27528665af4517c27b95
            $notifyId = Yii::$app->request->post('notify_id');

            //签名方式  MD5
            $signType = Yii::$app->request->post('sign_type');

            //签名    e7d51bf34a1317714d93fab13bbeab73
            $sign = Yii::$app->request->post('sign');

            //批次号
            $batchNo = Yii::$app->request->post('batch_no');

            //付款账号ID
            $payUserId = Yii::$app->request->post('pay_user_id');

            //付款账号姓名
            $payUserName = Yii::$app->request->post('pay_user_name');

            //付款账号
            $payAccountNo = Yii::$app->request->post('pay_account_no');

            //批量付款数据中转账成功的详细信息
            $successDetails = Yii::$app->request->post('success_details');

            //批量付款数据中转账失败的详细信息
            $failDetails = Yii::$app->request->post('fail_details');
            
				//请在这里加上商户的业务逻辑程序代
				    
				//判断是否在商户网站中已经做过了这次通知返回的处理
				//如果没有做过处理，那么执行商户的业务程序
				//如果有做过处理，那么不执行商户的业务程序

            return "success";
        } else {
            Yii::getLogger()->log('verify Notify failed', Logger::LEVEL_ERROR);
            return "fail";
        }
    }

}
```

