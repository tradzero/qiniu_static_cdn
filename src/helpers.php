<?php

if (!function_exists('cdnAsset')) {
    function cdnAsset($path, $prefix = "public")
    {
        $domain = env('QINIU_URL', '');
        if (0 !== stripos($domain, 'https://') && 0 !== stripos($domain, 'http://')) {
            $domain = "http://{$domain}";
            $domain = rtrim($domain, '/').'/';
        }
        $prefix = rtrim($prefix, '/') . DIRECTORY_SEPARATOR;
        $path = PHP_OS !==  'WINNT' ? : urlencode($prefix . $path);
        return $domain . $path;
    }
}
