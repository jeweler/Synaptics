<?php
class helpers {
	static function redirect($url, $time = 0) {
		header('Refresh: 0; url=' . $url);
		ob_end_flush();
		exit();
	}
	static function ip() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	static function route($controller = "", $action = "", $array = array(), $with = false) {
		include ("./routes.php");
		$inputvars = array('controller' => $controller, 'action' => $action) + $array;
		foreach ($routes as $route) {
			preg_match_all('/{(.*?)}/', $route['string'], $routevarsnoval);
			$routevarsval = $route;
			unset($routevarsval['string']);
			if (array_intersect($routevarsval, $inputvars) == $routevarsval) {
				$left = array_diff_assoc($inputvars, $routevarsval);
				$leftkeys = array_keys($left);
				$novalkeys = $routevarsnoval[1];
				if ($novalkeys == $leftkeys) {
					$ret = $route;
					break;
				}
			}
		}
		if (isset($ret))
			foreach ($inputvars as $key => $newvar) {
				$ret['string'] = str_replace('{' . $key . '}', $newvar, $ret['string']);
			}
		
		return (isset($ret)) ? $with?'/'.$ret['string'].'.html':$ret['string'] : "";
	}

	static function getlocation($arr) {

		$i = 1;
		$res = '';
		foreach ($arr as $href => $loc) {
			$hr = is_numeric($loc) ? '#' : $loc;
			$res .= '<li><a href="' . $hr . '">' . $href . '</a>';
			if ($i !== count($arr))
				$res .= '<span class="divider">/</span>';
			$i++;
			$res .= '</li>';
		}
		return $res;
	}

	static function translitIt($str) {
		$tr = array(" " => "_", "А" => "A", "Б" => "B", "В" => "V", "Г" => "G", "Д" => "D", "Е" => "E", "Ж" => "J", "З" => "Z", "И" => "I", "Й" => "Y", "К" => "K", "Л" => "L", "М" => "M", "Н" => "N", "О" => "O", "П" => "P", "Р" => "R", "С" => "S", "Т" => "T", "У" => "U", "Ф" => "F", "Х" => "H", "Ц" => "TS", "Ч" => "CH", "Ш" => "SH", "Щ" => "SCH", "Ъ" => "", "Ы" => "YI", "Ь" => "", "Э" => "E", "Ю" => "YU", "Я" => "YA", "а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ж" => "j", "з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l", "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r", "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h", "ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "sch", "ъ" => "y", "ы" => "yi", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya");
		return strtr($str, $tr);
	}

}
?>