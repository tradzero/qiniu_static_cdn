<?php

namespace Tradzero\Uploader\Console;

use Tradzero\Uploader\Uploader;
use Illuminate\Console\Command;

class UploadStatic extends Command
{
    protected $signature = 'uploader:upload {--folder=}';

    protected $description = '初始化上传所有静态文件';

    protected $uploader;

    public function __construct()
    {
        $accessKey = env('QINIU_AK', '');
        $secretKey = env('QINIU_SK', '');
        $bucket = env('QINIU_BUCKET', '');
        $domain = env('QINIU_URL', '');

        $uploader = new Uploader(
            $accessKey,
            $secretKey,
            $bucket,
            $domain
        );
        $this->uploader = $uploader;
        parent::__construct();
    }

    public function handle()
    {
        $this->uploader->uploadFolderFiles('public');
    }
}
