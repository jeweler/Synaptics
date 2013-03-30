<?
function loadModule($link){
	if(!file_exists($link)){
		throw new Exception("Модуль ".$link." не существует");
	}
}
?>