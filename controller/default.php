<?
class DefaultController extends DefaultC{
	function constructor(){
		$configs = new mysql('configs');
		$configs->find(false, array('param'=>'deflogin'));
		define('deflogin', $configs->result[0]->value);
		$configs->find(false, array('param'=>'defpass'));
		define('defpass', $configs->result[0]->value);
		$defpass = $configs->result[0]->value;
		
		if (isset($_COOKIE['login']) and isset($_COOKIE['password'])) {
			$command = new mysql('command');
			$command->find(false, array('login'=>$_COOKIE['login'], 'and', 'password'=>$_COOKIE['password']));
			if($command->lastnum > 0){
				$this->controller->setTemp("auth", 1);
				$result =  $command->result[0];
				$this->controller->setTemp(array("authname"=>$result->name, "authsurname"=>$result->surname, "authpic"=>$result->smallpic, 'authid'=>$result->id));
			}
		}
		
	}
}
?>