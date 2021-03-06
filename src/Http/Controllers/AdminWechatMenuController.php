<?php

namespace Phpcxy\WechatManager\Http\Controllers;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use Encore\Admin\Tree\Tools;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Phpcxy\WechatManager\Models\AdminWechatMenu;
use Phpcxy\WechatManager\Models\AdminWechatReply;
use Phpcxy\WechatManager\Tools\ApplyMenu;
use Phpcxy\WechatManager\WechatManager;

class AdminWechatMenuController extends AdminController
{
    /**
     * @var array
     */
    private $types;

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '微信自定义菜单';

    public function __construct()
    {
        $this->types = [
            'view' => '链接',
            'text' => '文字',
            'click' => '事件',
        ];
    }

    public function index(Content $content)
    {
        $content->header('微信自定义菜单');
        $content->body(AdminWechatMenu::tree(function($tree) {
            $tree->tools(function (Tools $tools) {
                $tools->add(new ApplyMenu());
            });

            $tree->branch(function ($branch) {
                $label = WechatManager::keyToLabel($branch['type']);
                $text = $branch['type'] ? Arr::get($this->types, $branch['type']) : '一级菜单';
                return "{$branch['title']} <span class='label label-{$label}'>{$text}</span>";
            });
        }));

        return $content;
    }

    /**
     * Redirect to edit page.
     *
     * @param int $id
     *
     * @param Content $content
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($id, Content $content)
    {
        return redirect()->route('menu.edit', ['id' => $id]);
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new AdminWechatMenu);
        $form->text('title', __('名称'))->rules('required');
        $form->select('parent_id', '父级菜单')->options(AdminWechatMenu::selectOptions())
            ->rules('required', [
                'required' => '请选择父级菜单'
            ]);

        $form->select('type', '类型')->options($this->types)->help("如果是一级菜单下还有二级菜单，无需选择")
            ->rules(function($form) {
                if (request()->get('parent_id') > 0) {
                    return 'required';
                }
            }, [
                'required' => '菜单类型必填'
            ]);

        $form->textarea('value', '内容')->help("如果是一级菜单下还有二级菜单，无需填写")
            ->rules(function($form) {
                if (request()->get('type')) {
                    return 'required';
                }
            }, [
                'required' => '内容必填'
            ]);

        // 表单回调
        $form->saved(function (Form $form) {
            if ($form->type == 'text') {
                if (!$form->model()->key) {
                    $form->model()->key = 'M_' . Str::random(6);
                    $form->model()->save();
                }

                $reply = AdminWechatReply::where('key', $form->model()->key)->first();
                if ($reply) {
                    $reply->value = $form->value;
                    $reply->save();
                } else {
                    AdminWechatReply::create([
                        'key' => $form->model()->key,
                        'source' => 'menu',
                        'type' => 'text',
                        'value' => $form->value
                    ]);
                }
            }
        });

        return $form;
    }
}