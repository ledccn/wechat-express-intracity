# 说明


## 安装

`composer install ledc/intra-city`

## 在CRMEB单商户使用

增加全局函数
文件：app/common.php
```php
/**
 * 微信配送API
 * @return ExpressApi
 */
function wechat_express_api(): ExpressApi
{
    return Utils::api();
}
```
