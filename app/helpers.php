<?php

use Illuminate\Support\Facades\Session;

if (!function_exists('canView')) {
    function canView(string $key): bool
    {
        $permissions = session('permissions', []);
        return in_array($key, $permissions);
    }
}
