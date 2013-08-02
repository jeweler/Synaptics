<?php
	class Controller{
		var $core, $layout = null, $templatevars, $contentvars=array(), $content, $moduleconf;
		function __construct($core){
			$this->core = $core;
			$this->routes = $core->routes;
			$this->args = $core->routes->get;
			$this->content = "Укажите content в контроллере, либо создайте файл /view/".$this->routes->controller.'/'.$this->routes->action.'.html';
			$this->templatevars = array('add'=>'','addjs'=>'','addcss'=>'',"title"=>"Укажите заголовок в контроллере!!");
			$this->constructor();
		}
		function setTitle($text){
			$this->templatevars['title'] = $text;
		}
		function titlePush($text){
			$this->setTemp('title', $this->templatevars['title'].$text);
		}
		function getArg($name){
			return $this->argSet($name)?$this->args[$name]:false;
		}
		function argSet($name){
			return array_key_exists($name, $this->args);
		}
		function jsAdd($link){
			$this->templatevars['addjs'] .= htmlgen::js($link);
		}

		function cssAdd($link){
			$this->templatevars['addcss'] .= htmlgen::css($link);
		}
		function getTfold($layout = false){
			$layout = !$layout?$this->layout:$layout;
			if(is_array($layout)){
				unset($layout[count($layout)-1]);
				$tfold ="/templates/".implode("/", $layout);
			}elseif(is_string($layout)){
				$tfold = "/templates/default/";
			}else $tfold="";
			return $tfold;
		}
		function constructor(){
		}
		 function setTemp($var){
             if(is_object($var)) $var = (array)$var;
			if(is_array($var)){
				foreach($var as $ket=>$v){
					$this->templatevars[$ket]=$v;
				}
			}else{
				if(func_num_args()>1){
					$this->templatevars[$var]=func_get_arg(1);
				}
			}
		}
		 function setCont($var){
            if(is_object($var)) $var = (array)$var;
			if(is_array($var)){
				foreach($var as $ket=>$v){
					$this->contentvars[$ket]=$v;
				}
			}else{
				if(func_num_args()>1){
					$this->contentvars[$var]=func_get_arg(1);
				}
			}
		}
	}
?>