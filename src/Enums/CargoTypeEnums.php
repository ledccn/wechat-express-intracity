<?php

namespace Ledc\IntraCity\Enums;

/**
 * 物品类型
 */
class CargoTypeEnums
{
    /**
     * 快餐
     */
    const INT_1 = 1;
    /**
     * 药品
     */
    const INT_2 = 2;
    /**
     * 百货
     */
    const INT_3 = 3;
    /**
     * 生鲜
     */
    const INT_6 = 6;
    /**
     * 酒品
     */
    const INT_8 = 8;
    /**
     * 文件
     */
    const INT_12 = 12;
    /**
     * 蛋糕
     */
    const INT_13 = 13;
    /**
     * 鲜花
     */
    const INT_14 = 14;
    /**
     * 数码
     */
    const INT_15 = 15;
    /**
     * 服装
     */
    const INT_16 = 16;
    /**
     * 汽配
     */
    const INT_17 = 17;
    /**
     * 珠宝
     */
    const INT_18 = 18;
    /**
     * 饮料
     */
    const INT_32 = 32;
    /**
     * 证照
     */
    const INT_36 = 36;
    /**
     * 宠物用品
     */
    const INT_55 = 55;
    /**
     * 母婴用品
     */
    const INT_56 = 56;
    /**
     * 美妆用品
     */
    const INT_57 = 57;
    /**
     * 家居建材
     */
    const INT_58 = 58;
    /**
     * 其他
     */
    const INT_99 = 99;

    /**
     * 枚举列表
     * @return string[]
     */
    public static function cases(): array
    {
        return [
            self::INT_1 => '快餐',
            self::INT_2 => '药品',
            self::INT_3 => '百货',
            self::INT_6 => '生鲜',
            self::INT_8 => '酒品',
            self::INT_12 => '文件',
            self::INT_13 => '蛋糕',
            self::INT_14 => '鲜花',
            self::INT_15 => '数码',
            self::INT_16 => '服装',
            self::INT_17 => '汽配',
            self::INT_18 => '珠宝',
            self::INT_32 => '饮料',
            self::INT_36 => '证照',
            self::INT_55 => '宠物用品',
            self::INT_56 => '母婴用品',
            self::INT_57 => '美妆用品',
            self::INT_58 => '家居建材',
            self::INT_99 => '其他',
        ];
    }

    /**
     * 枚举列表
     * @return array
     */
    public static function list(): array
    {
        $rs = [];
        foreach (self::cases() as $value => $name) {
            $rs[] = compact("name", "value");
        }
        return $rs;
    }
}
