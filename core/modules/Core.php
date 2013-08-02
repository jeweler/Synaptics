<?php
class Core{
	var $routes, $controller, $modules, $query;
	private function checkFile(){
		if(preg_match_all("/^(.*?)\.([0-9\w]+?)$/", $this->query, $values)){
			$query = $values[1][0];
			$format = $values[2][0];
		}else{
			$query = $this->query;
			$format = "";
		}
		if($format == 'css'){
			
			if(preg_match("/^[^\\:*?\"<>|]+$/", $query)){
				if(strlen($query)<256){
					if(file_exists(DIR.'/includes/css/'.$query.'.css.less')){
						$less = new Lessc();
						header('Content-type: text/css');
						die($less->compileFile(DIR.'/includes/css/'.$query.'.css.less'));
					}elseif(file_exists(DIR.'/includes/css/'.$query.'.css')){
						header('Content-type: text/css');
						die(file_get_contents(DIR."/includes/css/".$query.'.css'));
					}else{
						die("");
					}
				}
			}
		}
		if($format == 'js'){
			if(preg_match("/^[^\\:*?\"<>|]+$/", $query)){
				if(strlen($query)<256){
					if(file_exists(DIR.'/includes/javascript/'.$query.'.js.coffee')){
						$file = file_get_contents(DIR.'/includes/javascript/'.$query.'.js.coffee');
						header('Content-type: text/javascript');
						CoffeeScript\Init::load();
						die(CoffeeScript\Compiler::compile($file, array('filename' => $query.'.js.coffee')));
						
					}elseif(file_exists(DIR.'/includes/javascript/'.$query.'.js')){
						header('Content-type: text/javascript');
						die(file_get_contents(DIR."/includes/javascript/".$query.'.js'));
					}else{
						die("");
					}
				}
			}
		}
		if(is_file(urldecode(DIR.'public/'.$this->query))){
			$type = mime_content_type(urldecode(DIR.'public/'.$this->query));
			header('Content-type: '.$type);
			die(file_get_contents(urldecode(DIR.'public/'.$this->query)));
		}
	}
	function __construct($query){
        session_start();
        $query = array_shift(explode('?', $query));
		$this->query = str_replace(array('../', './'), '', ($query));
		ob_start();
		$config = YAML::YAMLLoad(DIR.'/core/configs/routes.yaml');
		if(isset($config['root']) and $query == "") $query = $config['root'];


        set_error_handler("myErrorHandler");
		$this->checkFile();

		$this->routes = new Routes($query);

		$this->checkAction();
		$action=$this->routes->action;
		
		if(file_exists(DIR.'/controller/default.php')){
			require_once (DIR.'/controller/default.php');
			$defaultController = new DefaultControllerС($this);
		}

		$this->controller->$action();
		$this->showView();
	}
	private function loadModules($names){
		if(!is_array($names)) new Except(new Exception('Ошибка модулей!'));
			foreach($names as $name){
				if(file_exists(DIR.'/core/modules/'.$name.'.php')){
					include_once(DIR.'/core/modules/'.$name.'.php');
					$this->modules []= $name;
				}
			}
	}
	private function showView(){
		try{
			$layout = $this->controller->layout;
			$view = DIR.'/view/'.$this->routes->controller.'/'.$this->routes->action.'.html';
			
			if(is_array($layout)){
				$dir = $layout;
				unset($dir[count($dir)-1]);
				$layout = DIR.'/templates/'.implode('/', $layout).'.html';
				if(!file_exists($layout)) throw  new Exception('Файл '.$layout.' не существует!');
				$vars = $this->controller->contentvars;
				$vars['tfold'] = $this->controller->getTfold();
				
				$content = file_exists($view)?hEtml::fromFile($view, $vars):hEtml::compile($this->controller->content, $vars);
				echo hEtml::fromFile($layout, array_merge($this->controller->templatevars, array('content'=>$content)));
				
			}elseif(is_null($layout)){
				echo hEtml::compile($this->controller->content, $this->controller->contentvars);
			}else{
				
				$layout = DIR.'/templates/default/'.$layout.'.html';
				if(!file_exists($layout)) throw  new Exception('Файл '.$layout.' не существует');
				$content = file_exists($view)?hEtml::fromFile($view, $this->controller->contentvars):$this->controller->content;
				echo hEtml::fromFile($layout, array_merge($this->controller->templatevars, array('content'=>$content)));
				
			}
		}catch(Exception $e){
			new Except($e);
		}
	}
	private function checkAction(){
		$controllerlink = DIR.'/controller/'.$this->routes->controller.'_controller.php';
		$controllername = ucfirst(strtolower($this->routes->controller)).'Controller';
		$viewlink=DIR.'/controller/'.$this->routes->controller.'_controller.php';
		try{
			if(!is_file($controllerlink)) throw new Exception('Файла контроллера '.$controllername.' не существует('.$controllerlink.')');
			include_once($controllerlink);
			if(!class_exists($controllername)) throw new Exception('В файле: '.$controllerlink.' не инициализирован класс '.$controllername.'<br>Пожалуйста, создайте его <br><br>class '.$controllername.' extends Controller{<br><br>//ваш код<br><br>}');
			$parents = array_values(class_parents($controllername));
			if(!(count($parents) == 1 and $parents[0] == 'Controller')) throw new Exception('Класс '.$controllername.' должен быть дочерним классом класса Controller');
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