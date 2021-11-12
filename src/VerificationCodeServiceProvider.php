<?php

namespace Zbxin\VerificationCode;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class VerificationCodeServiceProvider extends ServiceProvider
{

    /**
     * 注册验证器管理工具
     */

    public function register()
    {
        $this->app->singleton(VerificationCodeManager::class, function () {
            return new VerificationCodeManager();
        });
        $this->app->alias(VerificationCodeManager::class, 'verification_code');
    }

    /**
     * 增加对应的验证器
     */

    public function boot()
    {
        Validator::extend('verification_code', 'Zbxin\VerificationCode\Validators\VerificationCodeValidator@validator');
        Validator::extend('verification_code_id', 'Zbxin\VerificationCode\Validators\CodeIdValidator@validator');
    }
}
