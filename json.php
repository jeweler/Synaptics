<?
$a = json_encode(array(1=>2));
$l = yaml_emit($a);
var_dump($l);
?>
