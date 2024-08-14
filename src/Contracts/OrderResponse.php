<?php

namespace Ledc\IntraCity\Contracts;

/**
 * 创建配送单，响应参数
 */
class OrderResponse
{
    /**
     * 微信门店编号
     * @var string
     */
    public string $wx_store_id;
    /**
     * 微信订单编号
     * @var string
     */
    public string $wx_order_id;
    /**
     * 门店订单编号
     * @var string
     */
    public string $store_order_id;
    /**
     * 配送运力
     * @var string
     */
    public string $service_trans_id;
    /**
     * 配送距离（单位：米）
     * @var int
     */
    public int $distance;
    /**
     * 运力订单号
     * @var string
     */
    public string $trans_order_id;
    /**
     * 运力配送单号（是否返回取决于运力）
     * @var string
     */
    public string $waybill_id = '';
    /**
     * 配送费（单位：分）
     * @var int
     */
    public int $fee;
    /**
     * 取货码
     * @var string
     */
    public string $fetch_code = '';
    /**
     * 取货序号
     * @var string
     */
    public string $order_seq = '';

    /**
     * 构造函数
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key) && !is_null($value)) {
                $this->{$key} = $value;
            }
        }
    }
}