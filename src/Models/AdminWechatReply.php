<?php

namespace Phpcxy\WechatManager\Models;

use Illuminate\Database\Eloquent\Model;

class AdminWechatReply extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_wechat_reply';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
        'source',
        'type',
    ];
}
