<?php

namespace Tradzero\Uploader\Console;

use Tradzero\Uploader\Uploader;
use Illuminate\Console\Command;
use File;

class UploadStatic extends Command
{
    protected $signature = 'uploader:upload
        {--folder=public : 选择上传的文件夹}';

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
        $folder = $this->option('folder');
        if (! File::exists($folder)) {
            $this->error('文件夹可能不存在');
            return ;
        }
        $this->uploader->initFolderUpload($folder);
        $bar = $this->output->createProgressBar($this->uploader->getFileCount());
        $files = $this->uploader->getFiles();
        foreach ($files as $item => $file) {
            $hash = $this->uploader->checkFileLastestOrUpload($file);
            if (!is_null($hash)) {
                $bar->advance();
            } else {
                $bar->finish();
                $this->error('上传出错');
                return ;
            }
        }
        $bar->finish();
        $this->info('上传成功');
    }
}
