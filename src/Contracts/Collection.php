<?php

namespace Ledc\IntraCity\Contracts;

use JsonSerializable;

/**
 * 数据集
 */
class Collection implements JsonSerializable
{
    /**
     * 数据集数据
     * @var array
     */
    protected array $items = [];

    /**
     * 构造函数
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $this->convertToArray($items);
    }

    /**
     * @param array $items
     * @return Collection
     */
    public static function make(array $items = []): Collection
    {
        return new static($items);
    }

    /**
     * 是否为空
     * @access public
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * 删除数组的最后一个元素（出栈）
     *
     * @access public
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->items);
    }

    /**
     * 删除数组中首个元素，并返回被删除元素的值
     *
     * @access public
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->items);
    }

    /**
     * 以相反的顺序返回数组。
     *
     * @access public
     * @return static
     */
    public function reverse(): Collection
    {
        return new static(array_reverse($this->items));
    }

    /**
     * 在数组结尾插入一个元素
     * @access public
     * @param mixed $value 元素
     * @param string|null $key KEY
     * @return void
     */
    public function push($value, string $key = null): void
    {
        if (is_null($key)) {
            $this->items[] = $value;
        } else {
            $this->items[$key] = $value;
        }
    }

    /**
     * 在数组开头插入一个元素
     * @access public
     * @param mixed $value 元素
     * @param string|null $key KEY
     * @return void
     */
    public function unshift($value, string $key = null): void
    {
        if (is_null($key)) {
            array_unshift($this->items, $value);
        } else {
            $this->items = [$key => $value] + $this->items;
        }
    }

    /**
     * 给每个元素执行个回调
     *
     * @access public
     * @param callable $callback 回调
     * @return $this
     */
    public function each(callable $callback): Collection
    {
        foreach ($this->items as $key => $item) {
            if (false === $callback($item, $key)) {
                break;
            }
        }

        return $this;
    }

    /**
     * 用回调函数处理数组中的元素
     * @access public
     * @param callable|null $callback 回调
     * @return static
     */
    public function map(callable $callback): Collection
    {
        return new static(array_map($callback, $this->items));
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return array_map(function ($value) {
            return $value instanceof JsonSerializable ? $value->jsonSerialize() : $value;
        }, $this->items);
    }

    /**
     * 转换成数组
     *
     * @access public
     * @param mixed $items 数据
     * @return array
     */
    protected function convertToArray($items): array
    {
        if ($items instanceof self) {
            return $items->all();
        }

        return (array)$items;
    }
}
