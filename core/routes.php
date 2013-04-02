<?php
	$routes = Array(
		Array('string' => 'index-{mounth}-{year}-{side}', 'controller'=>'index', 'action'=>'index'),
		Array('string' => 'index-news-{id}', 'controller'=>'index', 'action'=>'index'),
		Array('string' => 'press/{id}', 'controller'=>'index', 'action'=>'readPress'),
		Array('string' => 'admin/{action}', 'controller'=>'admin'),
		Array('string' => '{action}', 'controller'=>'index'),
		Array('string' => '{controller}/{action}')
	);
?>