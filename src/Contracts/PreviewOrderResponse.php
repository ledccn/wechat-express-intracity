<?php

namespace Ledc\IntraCity\Contracts;

/**
 * 查询运费，响应参数
 */
class PreviewOrderResponse
{
    /**
     * 运力公司ID
     * @var string
     */
    public string $service_trans_id;
    /**
     * 配送距离（单位：米）
     * @var int
     */
    public int $distance;
    /**
     * 预估配送费（单位：分）
     * @var int
     */
    public int $est_fee;
    /**
     * 商品预计送达时间
     * @description 时间戳类型，是否返回取决于运力公司，支持该字段的运力公司：SFTC
     * @var int|null
     */
    public ?int $expected_finished_time = null;
    /**
     * 配送时长（单位：分钟）
     * @description 从下单到完成配送所需时间，是否返回取决于运力公司，支持该字段的运力公司：SFTC
     * @var int|null
     */
    public ?int $promise_delivery_time = null;

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
