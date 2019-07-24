<?php

use Phpcxy\WechatManager\Http\Controllers\AdminWechatMenuController;
use Phpcxy\WechatManager\Http\Controllers\AdminWechatReplyController;

Route::resource('wechat/menu', AdminWechatMenuController::class);
Route::resource('wechat/reply', AdminWechatReplyController::class);