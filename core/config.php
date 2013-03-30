<?php
$mysql_conn = Array(
'host' => 'localhost',
'port' => 3306,
'user' => 'root',
'password' => 'lewejer',
'db' => 'sati',
'meta' => 'cs_'
);
$mysql_module = array(
'csco'=>
	array('news'=>
		array(
			'name'=>
				array(
					'type' => 'text',
					'minlength' => 5,
					'maxlenght' => 6,
					'regexpr' => '[^abc]+'
				)
		)
	)
)
?>