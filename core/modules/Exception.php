<?php
class Except{
	function __construct($message){
		$ret = ob_get_contents();
		ob_end_clean();	
		$title = (func_num_args()>1)?func_get_arg(1):'';
		$where=$message->getTrace();
		$wh = "";
		for($i = count($where)-1; $i>=0; $i--){
			$current = $where[$i];
			$file = isset($where[$i]['file'])?explode($_SERVER['DOCUMENT_ROOT'], $where[$i]['file']):array('','');
			$class = isset($current['class'])?$current['class']:'';
			$type = isset($current['type'])?$current['type']:'';
			$line = isset($where[$i]['line'])?$where[$i]['line']:'';
			$wh .= "В файле (".$file[1]."), в строке ".$line.' вызов '.$class.$type.$current['function'].'<br>';
			$lastline = isset($current['line'])?$current['line']:$line;
		}
		$file = explode($_SERVER['DOCUMENT_ROOT'], $message->getFile());
		$wh .= "В файле (".$file[1]."), в строке ".$message->getLine().'<br>';
		die(hEtml::fromFile('./modules/htmls/Exceptions.html', array(
		'line' => $ret,
		'where'=>$wh,
		'message'=>$message->getMessage(),
		'title'=>$title)));
	}
}
?>