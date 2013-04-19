<?php
function myErrorHandler($errno, $errstr, $errfile, $errline){
	new Except(new Exception($errstr, $errno));
}
set_error_handler("myErrorHandler");
include_once('./modules/Core.php');
$core = new Core(substr($_SERVER['REQUEST_URI'],1));
?>