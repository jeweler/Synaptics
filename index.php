<?php
$mdlList = glob('core/modules/*.php');
$result = array();
foreach($mdlList as $mdl){
    require_once($mdl);
    $result[]= end(explode('/', $mdl));
}
function myErrorHandler($errno, $errstr, $errfile, $errline){
    new Except(new Exception($errstr, $errno));
}
$configs = YAML::YAMLLoad('core/configs/config.yaml');
define('DIR', $configs['sitedir']);
define('DOMAIN', $configs['domain']);
$core = new Core(substr($_SERVER['REQUEST_URI'],1), $result);
?>