# qiniu_static_cdn
laravel静态文件上传到七牛云扩展包  

# 安装
```bash
$ composer require tradzero/qiniu_static_cdn:dev-master -vvv
```
更改laravel项目下/config/app.php
```php
'providers' => [
    // Other service providers...
    Tradzero\Uploader\UploaderServiceProvider::class,
],
```
将配置文件复制到项目目录下：
```bash
$ php artisan vendor:publish --provider=Tradzero\Uploader\UploaderServiceProvider
```
更改配置文件：
```php
<?php

return [
    'cache_prefix' => 'staticupload',
    
    'qiniu_accessKey' => env('QINIU_AK', ''), // 更改为七牛云的AccessKey 可以在七牛云 个人中心 密钥管理中找到
    'qiniu_secretKey' => env('QINIU_SK', ''), // 更改为七牛云的SecretKey 可以在七牛云 个人中心 密钥管理中找到
    'qiniu_bucket' => env('QINIU_BUCKET', ''), // 七牛云使用的存储空间名
    'qiniu_domain' => env('QINIU_URL', ''), // 七牛云使用的存储空间所对应的空间域名
];

```

# 使用

## 上传

在命令行使用laravel的命令行
```bash
$ php artisan uploader:upload #可以使用--folder指定目录 该目录相对于项目根目录
```
将自动遍历目录下的文件上传到七牛云  

## helper 帮助方法


```php
cdnAsset('/XX/XX') // 可以自动生成资源文件路径
```

# 感谢
这里借鉴了Overtrue的[flysystem-qiniu](https://github.com/overtrue/flysystem-qiniu]flysystem-qiniu)的部分上传代码

# License
MIT
