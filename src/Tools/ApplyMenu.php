<?php

namespace Phpcxy\WechatManager\Tools;

use Encore\Admin\Actions\Action;
use Phpcxy\WechatManager\Models\AdminWechatMenu;

class ApplyMenu extends Action
{
    public $name = '发布菜单';

    protected $selector = '.apply-menu';

    public function handle()
    {
        $model = new AdminWechatMenu();
        $menus = $model->toTree();

        $menuArray = [];
        foreach ($menus as $menu) {
            $array = [];
            // 一级菜单没有设置内容和子菜单就报错
            if ($menu['parent_id'] === 0 && (empty($menu['type']) && count($menu['children']) === 0)) {
                $menuArray = [];
                break;
            }

            $array['name'] = $menu['title'];

            // 子菜单的处理
            if (count($menu['children']) > 0) {
                foreach ($menu['children'] as $child) {

                    // 子菜单下还有子菜单 异常退出
                    if (count($child['children']) > 0) {
                        $menuArray = [];
                        break 2;
                    }

                    $sub = [];
                    $sub['name'] = $child['title'];

                    $this->generateMenuKeyValue($child, $sub);

                    $array['sub_button'][] = $sub;
                }
            } else {
                $array['type'] = $menu['type'];
                $this->generateMenuKeyValue($menu, $array);
            }

            $menuArray[] = $array;
        }

        if (count($menuArray) == 0) {
            return $this->response()->error("发布失败，请检查自定义菜单的结构");
        }

        $app = app('wechat.official_account');

        $ret = $app->menu->create($menuArray);
        if ($ret['errcode'] === 0) {
            return $this->response()->success('发布成功');
        } else {
            return $this->response()->error("发布失败：{$ret['errmsg']}");
        }
    }

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-danger apply-menu">发布菜单</a>
HTML;
    }

    public function dialog()
    {
        $this->confirm('确定发布菜单吗？');
    }

    /**
     * @param $menu
     * @param array $array
     * @return void
     */
    private function generateMenuKeyValue($menu, array &$array)
    {
        if ($menu['type'] === 'view') {
            $array['url'] = $menu['value'];
            $array['type'] = $menu['type'];
        } elseif ($menu['type'] === 'text') {
            $array['key'] = $menu['key'];
            $array['type'] = 'click';
        } else {
            $array['key'] = $menu['value'];
            $array['type'] = $menu['type'];
        }
    }
}