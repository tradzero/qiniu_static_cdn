<?php
namespace Tradzero\Uploader;

use Qiniu\Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use Qiniu\Http\Error;
use Qiniu\Etag;
use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Support\Facades\Log as Log;
use Illuminate\Support\Facades\Cache as Cache;
use Exception;

class Uploader
{
    protected $accessKey;
    protected $secretKey;
    protected $bucket;
    protected $domain;

    protected $uploader;
    protected $bucketer;
    protected $auth;

    protected $files;
    protected $fileCount;
    protected $proccessed;

    protected $blackLists = [
        'txt', 'htaccess', 'config', 'conf', 'php'
    ];

    public function __construct($accessKey, $secretKey, $bucket, $domain)
    {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->bucket = $bucket;
        $this->domain = $domain;
    }

    public function initFolderUpload($folder)
    {
        $this->files = $this->allFiles($folder);
    }
    
    public function checkFileLastestOrUpload($file)
    {
        if ($this->checkExistByCache($file)) {
            return [['hash' => '']];
        }

        list($response, $error) = $this->getBucketer()->stat($this->bucket, $file->getPathname());
        if ($error) {
            // 当获取不到该资源时 上传文件
            if ($error->code() == 612) {
                $result = $this->uploadFile($file);
                return $result[0]['hash'];
            } else {
                Log::info($error);
            }
        } else {
            $uploadHash = $response['hash'];
            $hash = Etag::sum($file->getRealPath())[0];
            // 如果获取到文件，判断其hash是否与当前hash相等
            if ($uploadHash === $hash) {
                return $uploadHash;
            } else {
                $this->delete($file->getPathname());
                $result = $this->uploadFile($file);
                return $result[0]['hash'];
            }
        }
    }

    public function checkExistByCache($file)
    {
        $modifyTime = $file->getMTime();
        $filename = $file->getPathname();
        return Cache::has(config('staticupload.cache_prefix') . $filename . $modifyTime);
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function getUploader()
    {
        return $this->uploader ? : $this->uploader = new UploadManager();
    }

    public function getBucketer()
    {
        return $this->bucketer ? : $this->bucketer = new BucketManager($this->getAuth());
    }

    public function getAuth()
    {
        return $this->auth ? : $this->auth = new Auth($this->accessKey, $this->secretKey);
    }

    public function getFileCount()
    {
        return $this->fileCount;
    }

    private function allFiles($folder)
    {
        $fileSystem = new File;
        $allFile = $fileSystem->allFiles($folder);
        $this->fileCount = count($allFile);
        return $allFile;
    }
    private function uploadFile($file)
    {
        if ($file->getSize() <= 0 || in_array($file->getExtension(), $this->blackLists)) {
            return [['hash' => '']];
        } else {
            $modifyTime = $file->getMTime();
            $filename = $file->getPathname();
            $realPath = $file->getRealPath();
            Cache::forever(config('staticupload.cache_prefix') . $filename . $modifyTime, '1');
            return $this->getUploader()
            ->putFile(
                $this->getAuth()->uploadToken($this->bucket),
                $filename,
                $realPath
            );
        }
    }

    private function delete($file)
    {
        $response = $this->getBucketer()->delete($this->bucket, $file);
        return is_null($response);
    }
}
