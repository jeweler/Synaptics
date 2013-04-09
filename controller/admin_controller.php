<?php
class AdminController extends Controller {
	function constructor() {
		$this -> setTitle("Admin Panel: ");
		$this -> layout = array('charisma', 'blank');
		if ($this -> routes -> action !== "login") {
			if (!isset($_COOKIE['login']) or !isset($_COOKIE['password'])) {
				helpers::redirect('/' . helpers::route('admin', 'login') . '.html');
			} else {

				$configs = new mysql('configs');
				$configs -> find(false, array('param' => 'deflogin'));
				$deflogin = $configs -> result[0] -> value;
				$configs -> find(false, array('param' => 'defpass'));
				$defpass = $configs -> result[0] -> value;
				if (!($_COOKIE['login'] == $deflogin and $_COOKIE['password'] == md5($defpass))) {
					setcookie('login', '');
					setcookie('password', '');
					helpers::redirect('/' . helpers::route('admin', 'login') . '.html');
				}
			}
		}
	}
	function logout() {
		setcookie('login', null, time() + 31556926, '/');
		setcookie('password', null, time() + 31556926, '/');
		helpers::redirect(helpers::route('admin', 'login', false, 1));
	}

	function login() {
		$this -> layout = array('charisma', 'login');
		$this -> titlePush("Авторизация");
		if ($_POST) {
			$tries = new mysql('tries');
			$configs = new mysql('configs');
			$configs -> find(false, array('param' => 'deflogin'));
			$deflogin = $configs -> result[0] -> value;
			$configs -> find(false, array('param' => 'defpass'));
			$defpass = $configs -> result[0] -> value;
			$this -> content = "";
			$this -> layout = null;
			$tries -> find(false, array('ip' => helpers::ip(), 'and', 'time >', date("U") - 300));
			if (isset($_POST['login']) and isset($_POST['password'])) {

				if ($_POST['login'] == $deflogin and $_POST['password'] == $defpass) {

					if ($tries -> lastnum > 5) {
						$tries -> save(array("ip" => helpers::ip(), 'time' => date('U')));
						$this -> content = json_encode(array('state' => 'error', 'num' => 2));
					} else {
						$tries -> save(false, array("ip" => helpers::ip()));
						$this -> content = json_encode(array('state' => 'success'));
						setcookie('login', $_POST['login'], time() + 31556926, '/');
						setcookie('password', md5($_POST['password']), time() + 31556926, '/');
					}
				} else {
					$tries -> save(array("ip" => helpers::ip(), 'time' => date('U'), 'try' => $_POST['login'] . '-' . $_POST['password']));
					$this -> content = json_encode(array('state' => 'error', 'num' => 1));
				}
			} else {
				$this -> content = json_encode(array('state' => 'error', 'num' => 1));
			}

		}
	}

	function index() {
		$this -> setTemp('position', helpers::getlocation(array('Главная' => "/admin/index.html")));
		$this -> titlePush("Главная");
	}

}
?>