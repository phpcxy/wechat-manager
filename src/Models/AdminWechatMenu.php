<?php

namespace Phpcxy\WechatManager\Models;

use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;

class AdminWechatMenu extends Model
{
    use ModelTree, AdminBuilder;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_wechat_menu';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'parent_id',
        'order',
        'type',
        'key',
        'value',
    ];

//    public function getValueAttribute($value)
//    {
//        if ($this->type === 'text') {
//            $res = AdminWechatReply::where('key', $value)->first();
//            return $res->value;
//        } else {
//            return $value;
//        }
//    }
}
