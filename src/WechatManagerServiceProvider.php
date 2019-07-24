<?php

namespace Phpcxy\WechatManager;

use Illuminate\Support\ServiceProvider;

class WechatManagerServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(WechatManager $extension)
    {
        if (! WechatManager::boot()) {
            return ;
        }

        if ($this->app->runningInConsole() && $migrations = $extension->migrations()) {
            $this->publishes([$migrations => database_path('migrations')], 'wechat-manager-migrations');
        }

        $this->app->booted(function () {
            WechatManager::routes(__DIR__.'/../routes/web.php');
        });
    }
}