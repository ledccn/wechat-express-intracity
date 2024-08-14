<?php

namespace Ledc\IntraCity\Enums;

/**
 * 订单状态
 */
class OrderStatusEnums
{
    /**
     * 订单创建成功
     */
    const UINT_10000 = 10000;
    /**
     * 商家取消订单
     */
    const UINT_20000 = 20000;
    /**
     * 配送方取消订单
     */
    const UINT_20001 = 20001;
    /**
     * 配送员接单
     */
    const UINT_30000 = 30000;
    /**
     * 配送员到店
     */
    const UINT_40000 = 40000;
    /**
     * 配送中
     */
    const UINT_50000 = 50000;
    /**
     * 配送员撤单
     */
    const UINT_60000 = 60000;
    /**
     * 配送完成
     */
    const UINT_70000 = 70000;
    /**
     * 配送异常
     */
    const UINT_90000 = 90000;

    /**
     * 订单状态文字描述
     * @param int $value
     * @return string
     */
    public static function text(int $value): string
    {
        $enums = static::cases();

        return $enums[$value] ?? '未知状态：' . $value;
    }

    /**
     * 枚举列表
     * @return string[]
     */
    public static function cases(): array
    {
        return [
            self::UINT_10000 => '订单创建成功',
            self::UINT_20000 => '商家取消订单',
            self::UINT_20001 => '配送方取消订单',
            self::UINT_30000 => '配送员接单',
            self::UINT_40000 => '配送员到店',
            self::UINT_50000 => '配送中',
            self::UINT_60000 => '配送员撤单',
            self::UINT_70000 => '配送完成',
            self::UINT_90000 => '配送异常',
        ];
    }
}
