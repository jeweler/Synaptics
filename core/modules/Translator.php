<?php
class hEtml {
	private static $functions = array();
	public static function fromFile($filename, $vars = array()){
		return (file_exists($filename))? self::compile(file_get_contents($filename), $vars) : self::compile($filename, $vars);
	}
	public static function compile($text, $vars = array()) {
		self::$functions = YAML::YAMLLoad('configs/htmlfunctions.yaml');
		return self::search_first($text, $vars);
	}
	private static function search_first($text, $vars) {
		foreach (self::$functions as $sfunction) {
			$function = $sfunction['function'];
			$end = isset($sfunction['end']) ? (bool)$sfunction['end'] : false;
			$method = $sfunction['method'];
			$regexpr = '/' . self::gen_preg($function, $end) . '/s';
			$compiled = preg_match_all($regexpr, $text, $values);
			$params = array();
			if ($compiled) {

				for ($i = 0; $i < count($values[0]); $i++) {
					$text = str_replace($values[0][$i], self::$method(self::get_params($function, $values[0][$i], $end), $vars), $text);
				}
			}
		}
		return $text;
	}
	private static function date($params, $vars){
		if(isset($params['var']) && isset($vars[$params['var']]))
			return Module::date($vars[$params['var']]);
		else return Module::date($params['var']);
	}
	private static function render($params) {
		return "";
	}

	private static function eachs($params, $vars) {
		$return = "";
		if (isset($vars[$params['var']])) {
			foreach($vars[$params['var']] as $key=>$value){
				$n = $params['code'];
				$n = preg_replace('/% *key *%/', $key, $n);
				$n = preg_replace('/% *value *%/', $value, $n);
				$return .= $n;
			}
		}
		return $return;
	}
	private static function ifcond($params, $vars){
		$pars = preg_split("/[!><=]{1,2}/", $params['cond']);
		$ar = preg_match_all('/[!><=]{1,2}/', $params['cond'], $values);
		$ar = $values[0][0];
		$var = preg_match('/^_.+$/', $pars[0]);
		$f = $pars[0];
		$s = $pars[1];
		if($var)
			if(isset($vars[substr($f,1)]))
				$f = $vars[substr($f,1)];
		$var = preg_match('/^_.+$/', $pars[1]);
		if($var)
			if(isset($vars[substr($s,1)]))
				$f = $vars[substr($s,1)];
		$result = false;
		switch($ar){
			case '>' :
				$result = $f > $s;
			break;
			case '<' : 
				$result = $f < $s;
			break;
			case '=' : 
			case '==' : 
				$result = $f == $s;
			break;
			case '>=' : 
				$result = $f>=$s;
			break;
			case '<=' : 
				$result = $f<=$s;
			break;
			case '!=' : 
				$result = $f!=$s;
			break;
		}
		$code = $params['code'];
		$code = preg_split("/% *else *%/", $code);
		if(count($code) == 1) $code[]='';
		return $result?$code[0]:$code[1];
	}
	private static function repeat($params) {
		return str_repeat($params['code'], $params['times']);
	}

	private static function variable($params, $vars) {
		if (isset($vars[$params['var']]) && (is_numeric($vars[$params['var']]) or is_string($vars[$params['var']])))
			return $vars[$params['var']];
	}

	private static function get_params($function, $text, $end) {
		$i = preg_match_all("/{([0-9\w]+?)}/", $function, $results);
		$return = array();
		if ($i) {
			$keys = $results[1];
			$regexpr = '/' . self::gen_preg($function, $end) . '/s';
			preg_match_all($regexpr, $text, $values);
			for ($j = 0; $j < count($keys); $j++) {
				$return[$keys[$j]] = $values[$j + 1][0];
			}
			if ($end) {
				$return['code'] = trim($values[count($values) - 1][0]);
			}
		}
		return $return;
	}

	private static function gen_preg($f, $end = false) {
		$regexpr = str_replace('(', '\(', $f);
		$regexpr = str_replace(')', '\)', $regexpr);
		$regexpr = preg_replace('/\\{([0-9\w]+?)\\}/', '([0-9\w_><=!-]+?)', $regexpr);
		$regexpr = str_replace(' ', ' *', $regexpr);
		if ($end)
			$regexpr .= '(.*?)% *end *%';
		return $regexpr;
	}

}
?>