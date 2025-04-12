<?php
    // Load all classes from the PHPMailer libs directory
    function loadPHPMailerClassesFromDirectory($dir) {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                require_once $file->getPathname();
            }
        }
    }

    spl_autoload_register(function ($class) {
        if (strpos($class, 'PHPMailer\\') === 0) {
            $relative_class = str_replace('PHPMailer\\', '', $class);
            
            $base_dirs = [
                __DIR__ . '/../libs/PHPMailer/PHPMailer/src/',
                __DIR__ . '/../libs/PHPMailer/PHPMailer/language/'
            ];

            foreach ($base_dirs as $base_dir) {
                $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }

            error_log("PHPMailer class file not found: " . $relative_class);
        }
    });
?>
