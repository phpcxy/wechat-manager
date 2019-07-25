<?php

namespace Phpcxy\WechatManager\Http\Controllers;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Arr;
use Phpcxy\WechatManager\Models\AdminWechatReply;
use Phpcxy\WechatManager\WechatManager;

class AdminWechatReplyController extends AdminController
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
    protected $title = '微信自定义回复';
    /**
     * @var array
     */
    private $source;

    public function __construct()
    {
        $this->types = [
            'text' => '文字'
        ];

        $this->source = [
            'menu' => '自定义菜单',
            'reply' => '关键字回复',
            'welcome' => '欢迎语'
        ];
    }

    protected function grid()
    {
        $grid = new Grid(new AdminWechatReply);

        $grid->model()->where('source', '!=', 'menu');

        $grid->column('id', __('ID'));
        $grid->column('key', __('关键字'));

        $source = $this->source;
        $grid->column('source', __('来源'))->display(function($value) use ($source) {
            return $source[$value];
        })->label(WechatManager::arrayToLabelArray($source));

        $types = $this->types;
        $grid->column('type', __('类型'))->display(function($type) use ($types) {
            return $types[$type];
        })->label('danger');

        return $grid;
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
        return redirect()->route('reply.edit', ['id' => $id]);
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new AdminWechatReply);

        // 表单内不创建menu来源
        $source = $this->source;
        Arr::forget($source, 'menu');
        $form->select('source', '来源')->options($source)->rules('required', [
            'required' => '回复来源必须选择'
        ]);

        $form->text('key', '关键字')->rules('required_unless:source,welcome', [
            'required_unless' => '关键字必须填写'
        ]);
        $form->textarea('value', '回复的内容')->rules('required', [
            'required' => '回复的内容必须填写'
        ]);
        $form->hidden('type')->default('text')->rules('required', [
            'required' => '回复的类型必须选择'
        ]);

        return $form;
    }
}