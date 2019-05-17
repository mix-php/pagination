<?php

namespace Mix\Pagination;

use Mix\Core\Bean\AbstractObject;

/**
 * Class Paginator
 * @package Mix\Pagination
 * @author LIUJIAN <coder.keda@gmail.com>
 */
class Paginator extends AbstractObject
{

    /**
     * 内容
     * @var array
     */
    public $items = [];

    /**
     * 总记录数
     * @var int
     */
    public $count;

    /**
     * 当前页码
     * @var int
     */
    public $page;

    /**
     * 每页数量
     * @var int
     */
    public $perPage;

    /**
     * 链接数量
     * @var int
     */
    public $links = 5;

    /**
     * 固定最小最大页码
     * @var bool
     */
    public $fixedMinMax = true;

    /**
     * 总页数
     * @var int
     */
    public $pages;

    /**
     * 初始化事件
     */
    public function onInitialize()
    {
        parent::onInitialize();
        // 计算总页数
        $this->pages = (int)ceil($this->count / $this->perPage);
    }

    /**
     * 是否显示
     * @return bool
     */
    public function display()
    {
        return $this->pages < 1 ? false : true;
    }

    /**
     * 有首页
     * @return bool
     */
    public function hasFirst()
    {
        if ($this->pages <= 1) {
            return false;
        }
        return $this->page == 1 ? false : true;
    }

    /**
     * 有上一页
     * @return bool
     */
    public function hasPrev()
    {
        if ($this->pages <= 1) {
            return false;
        }
        return !is_null($this->prev());
    }

    /**
     * 有下一页
     * @return bool
     */
    public function hasNext()
    {
        return !is_null($this->next());
    }

    /**
     * 有尾页
     * @return bool
     */
    public function hasLast()
    {
        if ($this->pages <= 1) {
            return false;
        }
        return ($this->page == $this->pages || !$this->pages) ? false : true;
    }

    /**
     * 上一页
     * @return int|null
     */
    public function prev()
    {
        $page = $this->page - 1;
        return $page < 1 ? null : $page;
    }

    /**
     * 下一页
     * @return int|null
     */
    public function next()
    {
        $page = $this->page + 1;
        return $page > $this->pages ? null : $page;
    }

    /**
     * 数字页码
     * @return array
     */
    public function numbers()
    {
        // 零页与一页
        $totalPages = $this->pages;
        if ($totalPages == 0) {
            return [];
        } elseif ($totalPages == 1) {
            return [(object)[
                'text'     => '1',
                'selected' => true,
            ],
            ];
        }
        // 多页
        $number      = $this->links > $totalPages ? $totalPages : $this->links;
        $leftNumber  = $number / 2;
        $leftNumber  = is_integer($leftNumber) ? ($leftNumber - 1) : (int)floor($leftNumber);
        $rightNumber = $number - $leftNumber - 1;
        $leftShort   = ($this->page - $leftNumber) < 1 ? true : false;
        $rightShort  = ($this->page + $rightNumber) > $totalPages ? true : false;
        $center      = (!$leftShort && !$rightShort) ? true : false;
        $data        = [];
        $numberRange = [];
        // 左边短
        if ($leftShort) {
            $numberRange = range(1, $number);
        }
        // 右边短
        if ($rightShort) {
            $startNumber = $totalPages - $number + 1;
            $numberRange = range($startNumber, $startNumber + ($number - 1));
        }
        // 居中
        if ($center) {
            $startNumber = $this->page - $leftNumber;
            $numberRange = range($startNumber, $startNumber + $number - 1);
        }
        // 生成数据
        foreach ($numberRange as $value) {
            $data[] = (object)[
                'text'     => $value,
                'selected' => ($value == $this->page) ? true : false,
            ];
        }
        // 固定最小最大数字
        if ($this->fixedMinMax) {
            $temp  = $data;
            $pop   = array_pop($temp);
            $shift = array_shift($temp);
            // 后面加省略号
            if (($leftShort || $center) && $pop->text < $totalPages) {
                $pop->text != ($totalPages - 1) and array_push(
                    $data,
                    (object)[
                        'text'     => 'ellipsis',
                        'selected' => false,
                    ]
                );
                array_push(
                    $data,
                    (object)[
                        'text'     => $totalPages,
                        'selected' => false,
                    ]
                );
            }
            // 前面加省略号
            if (($rightShort || $center) && $shift->text > 1) {
                $shift->text != 2 and array_unshift(
                    $data,
                    (object)[
                        'text'     => 'ellipsis',
                        'selected' => false,
                    ]
                );
                array_unshift(
                    $data,
                    (object)[
                        'text'     => 1,
                        'selected' => false,
                    ]
                );

            }
        }
        return $data;
    }

}
