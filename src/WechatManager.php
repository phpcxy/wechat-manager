<?php

namespace Phpcxy\WechatManager;

use Encore\Admin\Extension;
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
     * @param $content
     * @param string $source
     * @return mixed
     */
    public static function getReply($content, $source = 'reply')
    {
        $replyModel= new AdminWechatReply();
        $replies = $replyModel->where('source', $source)->where('key', $content)->get();

        if ($replies->count() > 0) {
            return $replies->random()->value;
        }
    }

}