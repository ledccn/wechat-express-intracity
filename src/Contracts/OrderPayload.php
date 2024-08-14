<?php

namespace Ledc\IntraCity\Contracts;

use JsonSerializable;

/**
 * 创建配送单，接口参数
 */
class OrderPayload implements JsonSerializable
{
    /**
     * 微信门店编号
     * @var string
     */
    public string $wx_store_id;
    /**
     * 门店订单编号
     * @description 同一个门店订单编号要保证唯一，相同的订单号会重入
     * @var string
     */
    public string $store_order_id;
    /**
     * 收货用户openid
     * @var string
     */
    public string $user_openid;
    /**
     * 收货用户地址经度
     * @var float
     */
    public float $user_lng;
    /**
     * 收货用户地址维度
     * @var float
     */
    public float $user_lat;
    /**
     * 收件人姓名
     * @var string
     */
    public string $user_name;
    /**
     * 收件人手机号（11位手机号或带区号的固话:020-8080880）
     * @var string
     */
    public string $user_phone;

    /**
     * 收件用户详细地市
     * @var string
     */
    public string $user_address;
    /**
     * 订单序号
     * @description 用于配送员快速寻找到匹配的商品
     * @var string
     */
    public string $order_seq = '';
    /**
     * 验证码类型
     * @description 0:不生成、1:生成取货码、2:生成收货码、3:两者都生成
     * @var int
     */
    public int $verify_code_type = 0;
    /**
     * 跳转商家订单页面路径
     * @description 物流轨迹页面跳转到商家小程序的订单页面路径参数，期望向用户展示商品订单详情
     * @var string
     */
    public string $order_detail_path;
    /**
     * 订单状态回调地址
     * @description 回调协议详细查看第三节
     * @var string
     */
    public string $callback_url = '';
    /**
     * 是否使用沙箱
     * @description 1:使用沙箱环境；使用测试沙箱环境，不需要充值运费就可以生成测试订单
     * @var int
     */
    public int $use_sandbox = 0;
    /**
     * 商品重量
     * @var CargoPayload
     */
    public CargoPayload $cargo;

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $items = get_object_vars($this);
        foreach ($items as $key => $value) {
            if ($value instanceof JsonSerializable) {
                $items[$key] = $value->jsonSerialize();
            }
        }
        return $items;
    }
}
