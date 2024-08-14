# 说明


## 安装

`composer install ledc/intra-city`

## 使用说明

开箱即用，只需要传入一个配置，初始化一个实例即可：

```php
use Ledc\IntraCity\Config;
use Ledc\IntraCity\ExpressApi;

//更多配置项，可以查看 配置管理类的属性 Ledc\IntraCity\Config
$config = [
    'appid' => '',
    'token' => '',
    'access_token' => function (string $appid) use ($miniProgramAccessToken) {
          return $miniProgramAccessToken->getToken();
     },
    'aes_sn' => '',
    'aes_key' => '',
    'rsa_sn' => '',
    'rsa_public_key' => '',
    'rsa_private_key' => '',
    'cert_sn' => '',
    'cert_key' => '',
    'callback_url' => '',
    'wx_store_id' => '',
    'order_detail_path' => '',
    'enable' => true,
    'use_sandbox' => true,
];

$api = new ExpressApi(new Config($config));
```

在创建实例后，所有的方法都可以有IDE自动补全；例如：

```php
//开通门店权限（无加密，可直接调用）
$api->apply();

//创建门店
$api->createStore();

//查询门店
$api->queryStore();

//更新门店
$api->queryStore();
```



## 二次开发

配置管理类：`Ledc\IntraCity\Config`

同城配送API：`Ledc\IntraCity\ExpressApi`

你可以继承`Ledc\IntraCity\Config`或`Ledc\IntraCity\ExpressApi`，扩展您需要的功能。



## 捐赠

![reward](reward.png)