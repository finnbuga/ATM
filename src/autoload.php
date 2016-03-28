<?php

// Autoload Classes
spl_autoload_register(function ($class) {
    $parts = explode('\\', $class);
    $filename = __DIR__ . '/' . end($parts) . '.php';
    if (file_exists($filename)) {
        require $filename;
    }
});
