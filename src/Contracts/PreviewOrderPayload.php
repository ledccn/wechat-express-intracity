<?php

namespace Ledc\IntraCity\Contracts;

use JsonSerializable;

/**
 * 查询运费，接口参数
 */
class PreviewOrderPayload implements JsonSerializable
{
    /**
     * 微信门店编号
     * @var string
     */
    public string $wx_store_id;
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
     * 收件用户位置经度
     * @var float
     */
    public float $user_lng;
    /**
     * 收件用户位置维度
     * @var float
     */
    public float $user_lat;
    /**
     * 收件用户详细地市
     * @var string
     */
    public string $user_address;
    /**
     * 商品重量
     * @var CargoPayload
     */
    public CargoPayload $cargo;
    /**
     * 是否使用沙箱
     * @var int
     */
    public int $use_sandbox = 0;

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
        if (empty($this->use_sandbox)) {
            unset($items["use_sandbox"]);
        }
        return $items;
    }
}
