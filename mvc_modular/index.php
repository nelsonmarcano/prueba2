<?php
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', realpath(dirname(__FILE__)).DS);
define('APP_PATH', ROOT.'application'.DS);

try {
    
    require_once APP_PATH.'Autoload.php';
    require_once APP_PATH.'Config.php';
    
    Session::init();
    
    $registry = Registry::getInstancia();
    $registry->_request = new Request();
    $registry->_db = new Database();
    //$registry->_db2 = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_CHAR);
    $registry->_acl = new Acl();
    
    Bootstrap::run($registry->_request);
    
} catch (Exception $exc) {
    echo $exc->getMessage();
}
?>