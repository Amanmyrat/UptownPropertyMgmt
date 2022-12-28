<?php

namespace App\Library\Services;

class PythonHelper
{
    private $pypath = "python3";

    public function __construct()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // $this->pypath = "C:\Users\Aman\AppData\Local\Programs\Python\Python39\python.exe";
            $this->pypath = "C:\Users\Test\AppData\Local\Programs\Python\Python39\python.exe";
        }
    }

    public function getCollectionVacancyReport($scriptName, ...$args)
    {
        $python = ' ' . $this->console_scripts_path($scriptName) . ' "';
        $excelCollection = json_encode($args[0]);
        $header = json_encode($args[1]);
        $vacancyCollection = json_encode($args[2]);
        $vacancyHeader = json_encode($args[3]);
        $path = storage_path() . "/app/public/COLLECTIONS & VACANCY REPORT.xlsx";

        $cmd = $this->pypath . $python . $excelCollection . '" "' . $header . '" "'  .$vacancyCollection . '" "'  . $vacancyHeader . '" "' . $path . '"';

        exec($cmd, $out, $ret);
        return $out;
    }

    function console_scripts_path($filename = null)
    {
        $path = app_path('Console' . DIRECTORY_SEPARATOR . 'Scripts' . DIRECTORY_SEPARATOR) . $filename; // ie: /var/www/laravel/app/storage/json/filename.json
        return $path;
    }
}
