<?php
class files {
	static function renderfiles($files){
		$res = array();
		for($i = 0; $i<count($files['name']); $i++){
			$res[] = array('name'=>$files['name'][$i],
			'type'=>$files['type'][$i],
			'tmp_name'=>$files['tmp_name'][$i],
			'error'=>$files['error'][$i],
			'size'=>$files['size'][$i]);	
		}
		return $res;
	}
	static function uploadfile($uploaddir, $file, $newname = false) {
		$uploadfile = !$newname?$uploaddir . helpers::translitIt(basename($file['name'])):$uploaddir.helpers::translitIt($newname);
		
		$i=1;
		$lastname = explode('/', $uploadfile);
		$lastname = $lastname[count($lastname)-1];
		$filee = explode('.', $uploadfile);
		$ext = $filee[count($filee)-1];
		while(file_exists($uploadfile)){
			$f = $filee;
			$f[count($f)-1] = $i++;
			$f []= $ext;
			$uploadfile = implode('.', $f);
			$link = explode('/', $uploadfile);
			$lastname = $link[count($link)-1];
		}
		$ret = (copy($file['tmp_name'], $uploadfile))?$lastname:false;
		chmod($uploadfile, 0777);
		return $ret;
	}
	static function file_exist($file){
		return (strlen($file['name'])>0);
	}
}
?>