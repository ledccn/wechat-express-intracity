<?php

namespace Ledc\IntraCity\Contracts;

use JsonSerializable;

/**
 * 商品重量
 */
class CargoPayload implements JsonSerializable
{
    /**
     * 商品名称
     * @var string
     */
    public string $cargo_name;

    /**
     * 商品总重量（单位：克）
     * @var int
     */
    public int $cargo_weight;

    /**
     * 商品总价格（单位：分）
     * @var int
     */
    public int $cargo_price;

    /**
     * 商品类型（详见物品类型列表）
     * @var int
     */
    public int $cargo_type;

    /**
     * 商品数量
     * @var int
     */
    public int $cargo_num;
    /**
     * 物品列表，物品的图片和名称等，详见ItemDetail
     * @var Collection|CargoItemDetailPayload[]|array
     */
    public $item_list;

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
