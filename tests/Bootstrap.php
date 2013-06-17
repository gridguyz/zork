<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
if ((basename(realpath(__DIR__ . '/../..')) == 'webriq') && 
    (basename(realpath(__DIR__ . '/../../..')) == 'vendor') && 
    (is_file(__DIR__ . '/../../../autoload.php'))) 
{
    chdir(__DIR__ . '/../../../..');
} else {
    chdir(dirname(__DIR__));
}

// Setup autoloading
include 'vendor/autoload.php';
