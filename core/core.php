<?php
include_once('./modules/Html.php');
include_once('./modules/Exception.php');
function myErrorHandler($errno, $errstr, $errfile, $errline){
	new Except(new Exception($errstr, $errno));
}
set_error_handler("myErrorHandler");
include_once('./modules/Core.php');
$core = new Core($_GET['stringjustforcore']);
?>