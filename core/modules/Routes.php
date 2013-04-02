<?php
class Routes {
	var $controller;
	var $action;
	var $get;
	var $query;
	var $routes; 
	function __construct($string){
		if(class_exists('YAML')){
			$this->routes = YAML::YAMLLoad('routes.yaml');
		}else{
			include("./routes.php");
			$this->routes=$routes;
		}
		$setted = false;
		$this->query=$string;
		try{
			foreach($this->routes as $route){
				if($this->Match($route['string'])){
					$vars = (count($this->GetVars($route['string']))>0)?array_combine($this->GetVars($route['string']), $this->GetVals($route['string'])):array();
					if($this->isValid($route['string'], $route)){
						$this->setVars($vars , $route);
						$setted = true;
						break;
					}
				}
			}
			if(!$setted) throw new Exception("Не найден ни один путь!");
		}
		catch(Exception $e){
			new Except($e, "Ошибка путей!");
		}
		unset($this->lastregexpr);
		unset($this->routes);
	}
	private function setVars($array, $route){
		unset($route['string']);
		$result = array_merge($array, $route);
		$this->controller = $result['controller'];
		$this->action = $result['action'];
		unset($result['controller']);
		unset($result['action']);
		$this->get = $result;
	}
	private function isValid($string, $route){
		$strvars = $this->GetVars($string);
		unset($route['string']);
		$array = array_merge($strvars, array_keys($route));
		return (in_array('controller', $array) and in_array('action', $array));
	}
	function Match($string) {
		return is_array($this->GetVals($string, $this->query))?true:false;
	}
	private function array_vals_rec($array) {
		$ret = Array();
		foreach ($array as $item) {
			$ret[] = $item[0];
		}
		return $ret;
	}
	private function GetVals($string){
		
		if($this->query == $string)
			return array();
		
		$regexpr = '/^' . preg_replace('/{.+?}/', '([^\/]+?)', str_replace("/", "\/", $string)) . '$/';
		$good = preg_match_all($regexpr, $this->query, $reslist);
		unset($reslist[0]);
		return $good?$this -> array_vals_rec($reslist):false;
	}
	private function GetVars($string){
		preg_match_all('/{(.*?)}/', $string, $varlist);
		return $varlist[1];
	}

}
?>