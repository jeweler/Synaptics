<?php
	class htmlgen{
		private static function genattr($array){
			$return = '';
			foreach($array as $key=>$value){
				$return.=$key.'="'.$value.'" ';
			}
			$return = trim($return);
			return $return;
		}
		static function genview($a){
			if(!is_object($a)) new Except(new Exception("Ошибка, переменная переданная в http::genview($object) не объект"));
			foreach($a->columns() as $col){
				echo $col[0].'<br>';
			}
		}
		static function input($type="text", $value="", $params=array()){
			try{
				if(!in_array(strtolower($type), array('textarea', 'url','month', 'week', 'time','tel','search','color','date', 'datetime','datetime-local','email','number','range','button', 'checkbox', 'file', 'hidden', 'image', 'password', 'radio', 'reset', 'submit', 'text')))
					throw new Exception('Неизвестный тип input\'а');
				elseif(strtolower($type) == "textarea")
					return "<textarea ".htmlgen::genattr($params).'>'.$value.'</textarea>';
				else
				return '<input type="'.$type.'" value="'.$value.'"'. htmlgen::genattr($params).">";
			}catch(Exception $e){
				new Except($e);
			}
		}
		static function js($url="", $script=""){
			if($url == '' and $script == '') new Except(new Exception("Ни один из аргументов не передан"));
			$url = ($url !== "" and is_string($url))? " src='$url'":'';
			return "<script type='text/javascript'$url>".$script."</script>";
		}
		static function css($url='', $script=''){
			if($url == '' and $script == '') new Except(new Exception("Ни один из аргументов не передан"));
			$url = ($url !== "" and is_string($url))? " href=\"$url\"":'';
			return '<link type="text/css" rel="stylesheet"'.$url.'>';
		}
		static function table($array, $params=array()){
			try{
				if(!is_array($array)) throw new Exception('Аргумент array - не массив');
				$table = implode(array('<table ',htmlgen::genattr($params),'>'));
				foreach($array as $row){
					if(is_array($row)){
						$table .= '<tr>';
						foreach($row as $elem){
							$table .= implode(array('<td>', $elem, '</td>'));
						}
						$rable .= '</tr>';
					}
				}
			}catch(Exception $e){
				new Except($e);
			}
			$table .= '</table>';
			return $table;
		}
	}
?>