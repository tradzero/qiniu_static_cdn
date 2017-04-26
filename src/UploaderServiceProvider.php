<?php

namespace Tradzero\Uploader;

use Illuminate\Support\ServiceProvider;

class UploaderServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/staticupload.php' => config_path('staticupload.php'),
        ]);
    }

    public function register()
    {
        $this->app->singleton('command.uploader.upload', function ($app) {
            return new Console\UploadStatic();
        });
        $this->commands(['command.uploader.upload']);
    }
}
