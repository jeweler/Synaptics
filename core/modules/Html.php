<?php
class html {
	private static $file;
	private static $original;
	private static $vars;
	static function compile($url = "", $txt = "", $varss = array()) {
		if ($url == "" and $text = "")
			return "";
		if ($url !== "") {
			self::$original = file_get_contents($url);
		} else {
			self::$original = $txt;
		}
		self::$vars = $varss;
		return self::compil();
	}

	static function genClass($var, $array) {
		$ret = array();
		foreach ($array as $v) {
			$ret[] = (object) array($var => $v);
		}
		return $ret;
	}

	static function render($url = "", $txt = "", $varss = array(), $addvars = array()) {
		if ($url == "" and $text = "")
			return "";
		if ($url !== "") {
			self::$original = file_get_contents($url);
		} else {
			self::$original = $txt;
		}
		$edited = "";
		if (count($varss) > 0 and is_array($varss))
			foreach ($varss as $vars) {
				self::$vars = array_merge($addvars, (array)$vars);
				$edited .= self::compil();
			}
		self::$file = $edited;
		return $edited;
	}

	static function compil() {
		$file = self::$original;
		$vars = self::$vars;
		$orig = self::$original;
		$s = preg_match_all('/%[ ]*?render[ ]*?\([ ]*?([^,]+?)[ ]*?,[ ]*?(\w+?)[ ]*?\)[ ]*?%/', $file, $renders);
		if ($s > 0) {
			for ($i = 0; $i < count($renders[1]); $i++) {
				if (isset(self::$vars[$renders[2][$i]]) and file_exists('./../render/' . $renders[1][$i] . '.html')) {
					$addvars = self::$vars;	
					unset($addvars[$renders[2][$i]]);
					$nxt = self::render('./../render/' . $renders[1][$i] . '.html', "", self::$vars[$renders[2][$i]], $addvars);
					$file = str_replace($renders[0][$i], $nxt, $file);
				}else{
					$file = str_replace($renders[0][$i], "", $file);
				}
			}
		}
		self::$original = $orig;
		self::$vars = $vars;
		$s = preg_match_all('/%linkto[ ]+([^ ]+)[ ]+([^ ]+)((?:[ ]*[\w]+=>[\w]+[ ]*)*)%/', $file, $renders);
		if ($s > 0) {
			for ($i = 0; $i < count($renders[0]); $i++) {
				$controller = $renders[1][$i];
				$action = $renders[2][$i];
				$ads = array();
				$n = preg_match_all("#(?:(\w+)=>([\w]+))+#", $renders[3][$i], $varsed);
				for ($j = 0; $j < count($varsed[0]); $j++) {
					$check = preg_match_all('/_(\w+)/', $varsed[2][$j], $checkvars);
					$value = $check ? isset(self::$vars[$checkvars[1][0]]) ? self::$vars[$checkvars[1][0]] : '_' . $checkvars[1][0] : $varsed[2][$j];
					$ads[$varsed[1][$j]] = $value;
				}
				$file = str_replace($renders[0][$i], '/'.helpers::route($controller, $action, $ads).'.html', $file);
			}
		}
		self::$original = $orig;
		self::$vars = $vars;
		$s = preg_match_all("/%[ ]*repeat[ ]*([0-9]+?)[ ]*%(.*?)%[ ]*end[ ]*%/s", $file, $repeats);
		if ($s > 0) {
			for ($i = 0; $i < count($repeats[0]); $i++) {
				$file = str_replace($repeats[0][$i], str_repeat($repeats[2][$i], 0 + $repeats[1][$i]), $file);
				echo str_repeat($repeats[1][$i], 0 + $repeats[2][$i]);
			}
		}
		$s = preg_match_all('/%[ ]*substr[ ]+(\w+?)[ ]+(\w+?)[ ]+(\w+?)[ ]*%/', $file, $substrs);
		if($s>0){
			for($i=0; $i<count($substrs[0]); $i++){
				if(isset(self::$vars[$substrs[1][$i]])){
					$file = str_replace($substrs[0][$i], mb_substr(strip_tags(self::$vars[$substrs[1][$i]]), $substrs[2][$i], $substrs[3][$i]), $file);
				}
			}
		}
		$s = preg_match_all("/%each[ ]+(\w+?)[ ]+as[ ]+(\w+?)(=>(\w+?))?[ ]*%(.+?)%[ ]*end[ ]*%/s", $file, $eaches);
		if ($s > 0) {
			for ($i = 0; $i < count($eaches[0]); $i++) {
				if (isset(self::$vars[$eaches[1][$i]])) {
					if (is_array(self::$vars[$eaches[1][$i]])) {
						if (strlen($eaches[4][$i]) > 0) {
							$new = "";
							foreach (self::$vars[$eaches[1][$i]] as $key => $val) {
								$s = str_replace('%' . $eaches[2][$i] . '%', $key, $eaches[5][$i]);
								$s = str_replace('%' . $eaches[4][$i] . '%', $val, $s);
								$new .= $s;
							}
							$file = str_replace($eaches[0][$i], $new, $file);
							$file = str_replace($eaches[0][$i], $s, $file);
						} else {
							$s = "";
							foreach (self::$vars[$eaches[1][$i]] as $val) {

								$s .= trim(str_replace('%' . $eaches[2][$i] . '%', $val, $eaches[5][$i]));
							}
							$file = str_replace($eaches[0][$i], $s, $file);
						}
					}
				}else{
					$file = str_replace($eaches[0][$i], "", $file);
				}
			}
		}
		preg_match_all("/%if[ ]*?(\w+?)[ ]*?([!><=]{1,2})[ ]*?(\w+?)[ ]*?%(.+?)(%else%(.+?))?%end%/s", $file, $ifs);
		if (count($ifs[0]) > 0) {
			for ($i = 0; $i < count($ifs[0]); $i++) {
				$f = self::getVar($ifs[1][$i]);
				$s = self::getVar($ifs[3][$i]);
				switch($ifs[2][$i]) {
					case '>' :
						$res = $f > $s;
						break;
					case '<' :
						$res = $f < $s;
						break;
					case '==' :
						$res = $f == $s;
						break;
					case '>=' :
						$res = $f >= $s;
						break;
					case '<=' :
						$res = $f <= $s;
						break;
				}
				$file = str_replace($ifs[0][$i], $res ? $ifs[4][$i] : $ifs[6][$i], $file);
			}
		}

		foreach (self::$vars as $key => $var) {
			if (!is_object($var) and !is_array($var))
				$file = str_replace("%" . $key . "%", $var, $file);
		}
		return $file;
	}

	static function getVar($var) {
		return !isset(self::$vars[$var]) ? 0 : self::$vars[$var];
	}

}
?>