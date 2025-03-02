<?php

require_once __DIR__ . '/../vendor/autoload.php'; 

spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/../api/'; 
    $classPath = str_replace('\\', '/', $class) . '.php';
    $file = $baseDir . $classPath;

    if (file_exists($file)) {
        require_once $file;
    }
});
