<?php
class AdminController extends Controller
{
    function constructor()
    {
        header('Content-Type: text/html; charset=utf-8');
        $this->setTitle("Admin Panel: ");
        $this->layout = array('charisma', 'blank');
        $this->setTemp('flash', str_replace('\'', '\\\'', SESSION::getFlash()));
        if ($this->routes->action !== "login") {
            if (!isset($_COOKIE['login']) or !isset($_COOKIE['password'])) {
                helpers::redirect('/' . Routes::get('admin', 'login'));
            } else {
                $configs = new Model('configs');
                $configs->find(false, array('param' => 'deflogin'));
                $deflogin = $configs->result[0]->value;
                $configs->find(false, array('param' => 'defpass'));
                $defpass = $configs->result[0]->value;
                if (!($_COOKIE['login'] == $deflogin and $_COOKIE['password'] == md5($defpass))) {
                    setcookie('login', '');
                    setcookie('password', '');
                    helpers::redirect('/' . Routes::get('admin', 'login'));
                }
            }
        }
        $this->jsAdd('/colorpicker/colorpicker.js');
        $this->jsAdd('/colorpicker/eye.js');
        $this->jsAdd('/colorpicker/utils.js');
        $this->jsAdd('/colorpicker/layout.js');
        $this->cssAdd('/colorpicker/layout.js');
        $this->cssAdd('/colorpicker/colorpicker.css');

    }

    function logout()
    {
        setcookie('login', null, time() + 31556926, '/');
        setcookie('password', null, time() + 31556926, '/');
        helpers::redirect('/' . Routes::get('admin', 'login'));
    }

    function login()
    {
        $this->layout = array('charisma', 'login');
        $this->titlePush("Авторизация");
        if ($_POST) {
            $tries = new Model('tries');
            $configs = new Model('configs');
            $configs->find(false, array('param' => 'deflogin'));
            $deflogin = $configs->result[0]->value;
            $configs->find(false, array('param' => 'defpass'));
            $defpass = $configs->result[0]->value;
            $this->content = "";
            $this->layout = null;
            $tries->find(false, array('ip' => helpers::ip(), 'and', 'time >', date("U") - 300));
            if (isset($_POST['login']) and isset($_POST['password'])) {

                if ($_POST['login'] == $deflogin and $_POST['password'] == $defpass) {
                    if ($tries->insert > 5) {
                        $tries->add(array("ip" => helpers::ip(), 'time' => date('U')));
                        $this->content = json_encode(array('state' => 'error', 'num' => 2));
                    } else {
                        $tries->find(false, array('ip' => helpers::ip()));
                        $tries->delete();
                        $this->content = json_encode(array('state' => 'success'));
                        setcookie('login', $_POST['login'], time() + 31556926, '/');
                        setcookie('password', md5($_POST['password']), time() + 31556926, '/');
                    }
                } else {
                    $tries->add(array("ip" => helpers::ip(), 'time' => date('U'), 'try' => $_POST['login'] . '-' . $_POST['password']));
                    $this->content = json_encode(array('state' => 'error', 'num' => 1));
                }
            } else {
                $this->content = json_encode(array('state' => 'error', 'num' => 1));
            }

        }
    }

    function index()
    {
        $this->setTemp('position', helpers::getlocation(array('Главная' => "/admin/index")));
        $this->titlePush("Главная");
    }
}

?>
