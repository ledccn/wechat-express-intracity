<?php

namespace Ledc\IntraCity\Contracts;

use JsonSerializable;

/**
 * 物品列表，物品的图片和名称等，接口参数
 */
class CargoItemDetailPayload implements JsonSerializable
{
    /**
     * 物品名称
     * @var string
     */
    public string $item_name;
    /**
     * 物品图片
     * @var string
     */
    public string $item_pic_url;
    /**
     * 物品数量
     * @var int
     */
    public int $count;

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
