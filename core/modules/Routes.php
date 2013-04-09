<?php
class Routes{
	var $query, $routes, $type, $controller, $action;
	function __construct($query){
		$this->query = $query;
		$this->type = $_SERVER['REQUEST_METHOD'];
		$this -> types = YAML::YAMLLoad('configs/types.yaml');
		$routes = YAML::YAMLLoad('configs/routes.yaml');
		$this -> routes = $routes;
		$setted = false;
		foreach($routes as $route){
			$string = $route['string'];
			$vars = $this -> getVars($string);
			$types = $this -> getTypes($string);
			if(isset($route['via'])){
				if(in_array(strtolower($route['via']), array('post','get','put'))){
					if(strtolower($route['via']) !== strtolower($this->type))
						continue;
				}
			}
			$statics = isset($route['data'])?$route['data']:array();
			$regexprs = isset($route['regexpr'])?$route['regexpr']:array();
			if($this->is_valid($statics, $vars)){
				
				$regexpr = $this->getRegexpr($string);
				if(preg_match_all($regexpr, $query, $results)){
					
					$full = $this -> getFull($string, $query, $statics);
					foreach($types as $key=>$type){
						if(key_exists($type, $this -> types)){
							if(!preg_match('/'.$this->types[$type].'/', $full[$key])) continue;
						}
					}
					foreach($regexprs as $key=>$regexpt){
						if(key_exists($key, $full))
							if(!preg_match('/'.$regexpt.'/', $full[$key])){
								continue;
							}
					}
					if(!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $full['action']))
						continue;
					$this->controller = $full['controller'];
					$this->action = $full['action'];
					unset($full['controller']);
					unset($full['action']);
					$this->get = $full;
					$setted = true;
					break;
				}
			}
		}
		if(!$setted){
			new Except(new Exception('Не найден ни один маршрут по запросу "http://'.$_SERVER['HTTP_HOST'].'/'.$query.'.html:'.$this->type.'"'), 'Ошибка путей');
		}
	}
	function getFull($string, $request, $data){
		$f = $this->getVars($string);
		$s = $this->getVals($string, $request);
		return array_merge(array_combine($f, $s), $data);
	}
	function getRegexpr($string){
		$string = preg_quote($string);
		$string = str_replace("\\{", '{', $string);
		$string = str_replace("\\}", '}', $string);
		$string = str_replace("\\:", ':', $string);
		$string = str_replace("/", '\/', $string);
		$string = preg_replace('#\\{([1-9\w]+)(?::([1-9\w]+))?\\}#', '([^\/]+)', $string);
		return '/^'.$string.'$/';
	}
	function is_valid($array1, $array2){
		$arr = array_merge(array_keys($array1),$array2);
		return (in_array('controller', $arr) and in_array('action', $arr));
	}
	function getVals($string, $request){
		$count = preg_match_all('/{([1-9\w]+)(?::([1-9\w]+))?}/', $string, $results);
		if($count){
			$keys = $results[1];
			$regexprs = $this -> getRegexpr($string);
			if(count(preg_match_all($regexprs, $request, $values))){
				unset($values[0]);
				$ret = array();
				foreach($values as $val){
					$ret []= $val[0];
				}
				return $ret;
			}else{
				return array();
			}
		}else{
			return array();
		}
	}
	function getTypes($string){
		$count = preg_match_all('/{([1-9\w]+)(?::([1-9\w]+))?}/', $string, $results);
		$ret = array_combine($results[1], $results[2]);
		return $count?$ret:array();
	}
	function getVars($string){
		$count = preg_match_all('/{([1-9\w]+)(?::([1-9\w]+))?}/', $string, $results);
		return $count?$results[1]:array();
	}
}
?>