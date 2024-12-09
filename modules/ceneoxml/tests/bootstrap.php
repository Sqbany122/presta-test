<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// TODO: check include path
ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.dirname(__FILE__).'/../../../../../../phpbin');

define('__CENEO_XML_MODULE_PATH__', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR); 

// put your code here
function phpunitAutoload($class) {
    $classpath = str_replace('\\', DIRECTORY_SEPARATOR, strtolower($class)) . '.php';
    $filename = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 
        $classpath;

    if (file_exists($filename)) {
        require_once $filename;

    } else if (substr($class, -4) == 'Test') {
        $testfile = __DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . $classpath;

        if (file_exists($testfile)) {
            require_once $testfile;
        }
    }
}

spl_autoload_register('phpunitAutoload');
?>
