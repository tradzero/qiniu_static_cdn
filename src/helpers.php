<?php

if (!function_exists('cdnAsset')) {
    /**
     * 根据路径拼接CDN文件地址
     *
     * @param string $path 文件相对路径
     * @param string $prefix 选择相对于laravel文件目录的位置 默认为public
     * @return string path 
     */
    function cdnAsset($path, $prefix = "public")
    {
        $domain = config('staticupload.qiniu_domain');
        if (0 !== stripos($domain, 'https://') && 0 !== stripos($domain, 'http://')) {
            $domain = "http://{$domain}";
            $domain = rtrim($domain, '/').'/';
        }
        $prefix = rtrim($prefix, '/') . DIRECTORY_SEPARATOR;
        // handle windows separator problem
        $path = PHP_OS !==  'WINNT' ?
            : urlencode($prefix . ltrim(str_replace('/', DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR));
        return $domain . $path;
    }
}
