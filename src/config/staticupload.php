<?php

return [
    'cache_prefix' => 'staticupload',
    
    'qiniu_accessKey' => env('QINIU_AK', ''),
    'qiniu_secretKey' => env('QINIU_SK', ''),
    'qiniu_bucket' => env('QINIU_BUCKET', ''),
    'qiniu_domain' => env('QINIU_URL', ''),
];
