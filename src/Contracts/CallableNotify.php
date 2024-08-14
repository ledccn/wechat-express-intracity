<?php

namespace Ledc\IntraCity\Contracts;

use InvalidArgumentException;

/**
 * 回调协议
 */
class CallableNotify
{
    /**
     * 下单小程序appid
     * @var string
     */
    public string $appid;
    /**
     * 微信门店id
     * @var string
     */
    public string $wx_store_id;
    /**
     * 微信订单号
     * @var string
     */
    public string $wx_order_id;
    /**
     * 门店订单号
     * @var string
     */
    public string $store_order_id;
    /**
     * 订单状态
     * @var int
     */
    public int $order_status;
    /**
     * 订单状态变更时间
     * @var int
     */
    public int $status_change_time;
    /**
     * @var int 消息推送时间
     */
    public int $timestamp;
    /**
     * 运力ID
     * @var string
     */
    public string $service_trans_id;
    /**
     * 签名值
     * @var string
     */
    public string $sign;

    /**
     * 构造函数
     * @param array $attributes
     * @param string $token 小程序的安全token（设置路径：开发管理->开发设置->消息推送->Token）
     */
    public function __construct(array $attributes, string $token)
    {
        $this->verifySign($attributes, $token);
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key) && !is_null($value)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * 验证签名
     * @param array $attributes
     * @param string $token
     * @return void
     */
    protected function verifySign(array $attributes, string $token)
    {
        $sign = $attributes['sign'] ?? '';
        if (empty($sign)) {
            throw new InvalidArgumentException('sign字段不能为空');
        }

        unset($attributes['sign']);

        // 1.对报文的数据做预处理用=连接key和value组成键值对，按key的ascii码升序排列键值对用&链接
        $step = [];
        foreach ($attributes as $key => $value) {
            $step[] = $key . '=' . $value;
        }
        sort($step, SORT_STRING);
        $str = implode('&', $step);

        // 2.拼接小程序的安全token，安全token需要在下单小程序管理后台设置和获取，设置路径：开发管理->开发设置->消息推送->Token
        $str .= '&token=' . $token;

        // 3.对第二步中的字符串计算MD5，得到十六进制结果取小写
        if (md5($str) !== $sign) {
            throw new InvalidArgumentException('回调协议的签名验证失败：' . json_encode($attributes, JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * 响应报文（表示应答成功，如果没有接收到正确地请求应答，微信会重试回调）
     * @return array
     */
    public function response(): array
    {
        return [
            'return_code' => 0,
            'return_msg' => 'OK'
        ];
    }
}
