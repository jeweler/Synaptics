<?php
class Core{
	var $routes, $controller, $modules;
	function __construct($query){
		ob_start();
		$this->loadModules(array('MYSQL', 'Default', 'HtmlGenerator', 'Routes', 'Controller', 'Curl', 'Line', 'Mysql', 'Helpers', 'Files', 'Images'));
		$this->routes = new Routes($query);
		$this->checkAction();
		$action=$this->routes->action;
		if(file_exists('./../controller/default.php')){
			include_once('./../controller/default.php');
			$defaultController = new DefaultController($this);
		}
		$this->controller->$action();
		$this->showView();
	}
	private function loadModules($names){
		try{
		if(!is_array($names)) throw new Exception('Ошибка модулей!');
			foreach($names as $name){
				if(file_exists('./modules/'.$name.'.php')){
					include_once('./modules/'.$name.'.php');
					$this->modules []= $name;
				}
			}
		}catch(Exception $e){
			new Except($e);
		}
	}
	private function showView(){
		try{
			$layout = $this->controller->layout;
			$view = './../view/'.$this->routes->controller.'/'.$this->routes->action.'.html';
			
			if(is_array($layout)){
				$dir = $layout;
				unset($dir[count($dir)-1]);
				$layout = './../templates/'.implode('/', $layout).'.html';
				if(!file_exists($layout)) throw  new Exception('Файл '.$layout.' не существует!');
				$vars = $this->controller->contentvars;
				$vars['tfold'] = $this->controller->getTfold();
				$content = file_exists($view)?html::compile($view, '', $vars):html::compile('', $this->controller->content, $vars);
				echo html::compile($layout, '',  array_merge($this->controller->templatevars, array('content'=>$content, 'tfold'=>'/templates/'.implode('/', $dir))));
				
			}elseif(is_null($layout)){
				echo html::compile('',$this->controller->content, $this->controller->contentvars);
			}else{
				
				$layout = './../templates/default/'.$layout.'.html';
				if(!file_exists($layout)) throw  new Exception('Файл '.$layout.' не существует');
				$content = file_exists($view)?html::compile($view, '', $this->controller->contentvars):$this->controller->content;
				echo html::compile($layout, '', array_merge($this->controller->templatevars, array('content'=>$content, 'tfold'=>'/templates/default/')));
				
			}
		}catch(Exception $e){
			new Except($e);
		}
	}
	private function checkAction(){
		$controllerlink = './../controller/'.$this->routes->controller.'_controller.php';
		$controllername = ucfirst(strtolower($this->routes->controller)).'Controller';
		$viewlink='./../controller/'.$this->routes->controller.'_controller.php';
		try{
			if(!is_file($controllerlink)) throw new Exception('Файла контроллера '.$controllername.' не существует('.$controllerlink.')');
			include_once($controllerlink);
			if(!class_exists($controllername)) throw new Exception('В файле: '.$controllerlink.' не инициализирован класс '.$controllername.'<br>Пожалуйста, создайте его <br><br>class '.$controllername.' extends Controller{<br><br>//ваш код<br><br>}');
			$parents = array_values(class_parents($controllername));
			if(!(count($parents) == 1 and $parents[0] == 'Controller')) throw new Exception('Класс '.$controllername.' должен быть дочерним классом класса Controller');
			include('./config.php');
			$this->controller = new $controllername($this);
			if(!method_exists($this->controller, $this->routes->action)) throw new Exception('В классе '.$controllername.' не существует метода '.$this->routes->action.'()<br>Пожалуйста, создайте его <br><br>class '.$controllername.' extends Controller{<br><br>public function '.$this->routes->action.'(){<br><br>}<br><br>}');			
		}catch(Exception $e){
			new Except($e,'Ошибка!');
		}
	}
	function __destruct(){
		ob_end_flush();
	}
}
?>