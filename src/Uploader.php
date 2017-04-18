<?php
namespace Tradzero\Uploader;

use Qiniu\Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use Qiniu\Http\Error;
use Qiniu\Etag;
use Illuminate\Filesystem\Filesystem as File;
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

    public function __construct($accessKey, $secretKey, $bucket, $domain)
    {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->bucket = $bucket;
        $this->domain = $domain;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function initFolderUpload($folder)
    {
        $this->files = $this->allFiles($folder);
    }
    
    public function checkFileLastestOrUpload($file)
    {
        list($response, $error) = $this->getBucketer()->stat($this->bucket, $file->getPathname());

        if ($error) {
            // 当获取不到该资源时 上传文件
            if ($error->code() == 612 || $error->getResponse()->statusCode == 612) {
                $result = $this->uploadFile($file);
                return $result[0]['hash'];
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

    private function allFiles($folder)
    {
        $fileSystem = new File;
        $allFile = $fileSystem->allFiles($folder);
        $this->fileCount = count($allFile);
        return $allFile;
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

    private function uploadFile($file)
    {
        if ($file->getSize() <= 0) {
            return [['hash' => '']];
        } else {
            return $this->getUploader()
            ->putFile(
                $this->getAuth()->uploadToken($this->bucket),
                $file->getPathname(),
                $file->getRealPath()
            );
        }
    }

    private function delete($file)
    {
        $response = $this->getBucketer()->delete($this->bucket, $file);
        return is_null($response);
    }
}
