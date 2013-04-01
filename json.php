<?
$a = json_encode(array(1=>2));
$l = (json_decode($a) == null)?$a:json_decode($a);
var_dump($l);
?>
