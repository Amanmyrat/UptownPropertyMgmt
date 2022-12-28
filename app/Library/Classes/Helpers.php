<?php

if (!function_exists('open_json_file')) {
    /**
     * Opens Json file placed in /storage/json/ , deserializes it and returns
     */

    function open_json_file($filename)
    {
        $path = storage_path() . "/json/${filename}"; // ie: /var/www/laravel/app/storage/json/filename.json

        $json = json_decode(file_get_contents($path), true);
        return $json;
    }
}

if (!function_exists('console_scripts_path')) {
    /**
     * Opens Json file placed in /storage/json/ , deserializes it and returns
     */

    function console_scripts_path($filename = null)
    {
        $path = app_path('Console' . DIRECTORY_SEPARATOR . 'Scripts' . DIRECTORY_SEPARATOR) . $filename; // ie: /var/www/laravel/app/storage/json/filename.json

        return $path;
    }
}

function generateRandomString($length = 10)
{
    $characters = 'abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
