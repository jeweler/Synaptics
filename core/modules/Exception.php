<?php
class Except{
	function __construct($message){
		ob_end_clean();	
		$title = (func_num_args()>1)?func_get_arg(1):'';
		$where=$message->getTrace();
		$wh = "";
		for($i = count($where)-1; $i>=0; $i--){
			$current = $where[$i];
			$file = explode($_SERVER['DOCUMENT_ROOT'], $where[$i]['file']);
			$wh .= "В файле (".$file[1]."), в строке ".$where[$i]['line'].' вызов '.$current['class'].$current['type'].$current['function'].'<br>';
			$lastline = $current['line'];
		}
		$file = explode($_SERVER['DOCUMENT_ROOT'], $message->getFile());
		$wh .= "В файле (".$file[1]."), в строке ".$message->getLine().'<br>';
		die(html::compile('./modules/htmls/Exceptions.html', "", array(
		'where'=>$wh,
		'message'=>$message->getMessage(),
		'title'=>$title)));
	}
}
?>