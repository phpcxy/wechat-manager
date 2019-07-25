<?php

namespace Phpcxy\WechatManager;

use Encore\Admin\Extension;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Phpcxy\WechatManager\Models\AdminWechatReply;

class WechatManager extends Extension
{
    public $name = 'wechat-manager';

    public $migrations = __DIR__ . '/../database/migrations';

    public static function import()
    {
        parent::createMenu('公众号管理', '', 'fa-wechat');

        $menuModel = config('admin.database.menu_model');
        $id = $menuModel::max('id');

        parent::createMenu('自定义菜单', 'wechat/menu', 'fa-flickr', $id);
        parent::createMenu('自定义回复', 'wechat/reply', 'fa-commenting', $id);

        parent::createPermission('公众号管理', 'wechat', 'wechat*');
    }

    /**
     * 根据指定字符串的首字母算出label标签样式
     * @param $key
     * @return mixed
     */
    public static function keyToLabel($key)
    {
        $labels = ['danger', 'warning', 'info', 'primary', 'success', 'default'];

        $letter = substr($key, 0, 1);
        $ascii = ord($letter);
        $index = $ascii % 6;

        return $labels[$index];
    }

    /**
     * 数组转换label对应关系
     * @param $array
     * @return array
     */
    public static function arrayToLabelArray($array)
    {
        $labels = ['danger', 'warning', 'info', 'primary', 'success', 'default'];
        $colors = [];

        $i = 0;
        foreach ($array as $key => $value) {
            $colors[$key] = Arr::get($labels, $i, 'default');
            $i++;
        }
        return $colors;
    }

    /**
     * @param $content
     * @param string $source
     * @return mixed
     */
    public static function getReply($content, $source = 'reply')
    {
        $replyModel= new AdminWechatReply();
        $replies = $replyModel->where('source', $source)->when($content, function($query, $content) {
            return $query->where('key', $content);
        })->get();

        if ($replies->count() > 0) {
            return $replies->random()->value;
        }
    }

    /**
     * 返回关注欢迎语回复
     * @return mixed
     */
    public static function getWelcomeReply()
    {
        return self::getReply(null, 'welcome');
    }

}