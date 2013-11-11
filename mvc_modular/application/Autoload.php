<?php

function autoloadCore($class)
{
    if (file_exists(APP_PATH . $class . '.php')) {
        include_once APP_PATH. $class . '.php';
    }
}

function autoloadLibs($class)
{
    if (file_exists(ROOT . 'libs' . DS . 'class.' . strtolower($class) . '.php')) {
        include_once ROOT . 'libs' . DS . 'class.' . strtolower($class) . '.php';
    }
}

spl_autoload_register('autoloadCore');
spl_autoload_register('autoloadLibs');
?>
