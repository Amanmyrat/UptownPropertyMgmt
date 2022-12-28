<?php

namespace App\Library\Classes;

use Exception;
use Illuminate\Support\Facades\Redis;

class RedisHelper
{
    public static function get($key)
    {
        try {
            $response = Redis::get($key);
            return unserialize(base64_decode($response));
        } catch (Exception $exception) {
        }
        return null;
    }

    public static function set($key, $object)
    {
        Redis::set($key, base64_encode(serialize($object)));
    }
}
