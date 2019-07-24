laravel-admin 微信公众号管理插件
======

目前实现了简单的自定义菜单和自定义关键字回复功能

#### 安装
`composer require phpcxy/wechat-manager`

#### 发布资源
```
php artisan vendor:publish --tag=wechat-manager-migrations
```

#### 数据迁移

```
php artisan migrate
```

#### 发布菜单
```
php artisan admin:import wechat-manager
```

#### 配置微信
在.env写入以下配置
```
WECHAT_OFFICIAL_ACCOUNT_APPID=公众号appid
WECHAT_OFFICIAL_ACCOUNT_SECRET=公众号app secret
WECHAT_OFFICIAL_ACCOUNT_TOKEN=token
WECHAT_OFFICIAL_ACCOUNT_AES_KEY=
```

#### 使用说明

0. 使用[laravel-wechat](https://github.com/overtrue/laravel-wechat)调用微信SDK，具体使用请查看文档

1. 菜单使用了[model-tree](http://laravel-admin.org/docs/zh/model-tree)来管理，编辑好菜单后点击`发布菜单`按钮进行发布

2. 获取指定关键字的回复
```
$text = WechatManager::getReply('keyword');
```
即可返回该关键字设置的回复信息，如果该关键字有多个回复则会随机获取一个返回。

使用laravel-wechat的话，可以在微信消息服务端那里这样使用
```

/**
 * 接收微信消息和事件
 * @param Request $request
 * @return
 */
public function server(Request $request)
{
    $wechat = app('wechat.official_account');
    $wechat->server->push(function($message) {
  
        $type = $message['MsgType'];
        
        switch ($type) {
            case 'text':
                $content = $message['Content'];
                $reply = WechatManager::getReply($content);
                if ($reply) {
                    return $reply;
                }
                break;
            case 'event':
                // 菜单的点击回复使用了CLICK事件，所以需要在事件这里获取下回复内容
                if ($message['Event'] === 'CLICK') {
                    $reply = WechatManager::getReply($message['EventKey'], 'menu');
                    if ($reply) {
                        return $reply;
                    }
                }
                
                break;
            
            default:
                return 'hello world';
                break;
                
        }
        
    });
    
    return $this->wechat->server->serve();
}

```

