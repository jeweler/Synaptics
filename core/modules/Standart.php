<?
function loadModule($link){
	if(!file_exists($link)){
		throw new Exception("Файла ".$link." не существует");
	}
}
?>