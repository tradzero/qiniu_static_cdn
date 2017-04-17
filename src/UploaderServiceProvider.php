<?php

namespace Tradzero\Uploader;

use Illuminate\Support\ServiceProvider;
use \Log;
class UploaderServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    public function register()
    {
        $this->app->singleton('command.uploader.upload',
            function ($app) {
                return new Console\UploadStatic();
            }
        );
        $this->commands(['command.uploader.upload']);
    }
}
