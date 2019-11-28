<?php

if (!function_exists('wcp_asset')) {
    function wcp_asset($path = '')
    {
        return WC_PRIME_URL . 'public/' . $path;
    }
}
