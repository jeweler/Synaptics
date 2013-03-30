<?
class AdminController extends Controller {
	function constructor() {
		$this -> setTitle("CS Admin Panel: ");
		$this -> layout = array('charisma', 'blank');
		if ($this -> routes -> action !== "login") {
			if (!isset($_COOKIE['login']) or !isset($_COOKIE['password'])) {
				helpers::redirect('/' . helpers::route('admin', 'login') . '.html');
			}else{
				$configs = new mysql('configs');
				$configs->find(false, array('param'=>'deflogin'));
				$deflogin = $configs->result[0]->value;
				$configs->find(false, array('param'=>'defpass'));
				$defpass = $configs->result[0]->value;
				$command = new mysql('command');
				$command -> find(false, array('login' => $_COOKIE['login'], 'and', 'password' => $_COOKIE['password']));
				if(!($_COOKIE['login'] == $deflogin and $_COOKIE['password']==md5($defpass) or $command->lastnum > 0)){
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
		helpers::redirect('/' . helpers::route('admin', 'login') . '.html');
	}

	function login() {
		$this -> layout = array('charisma', 'login');
		$this -> titlePush(" Авторизация");
		if ($_POST) {
			$tries = new mysql('tries');
			$configs = new mysql('configs');
			$configs->find(false, array('param'=>'deflogin'));
			$deflogin = $configs->result[0]->value;
			$configs->find(false, array('param'=>'defpass'));
			$defpass = $configs->result[0]->value;
			$this -> content = "";
			$this -> layout = null;
			if (isset($_POST['login']) and isset($_POST['password'])) {
				if($_POST['login'] == $deflogin and $_POST['password']==$defpass){
					$tries -> save(false, array("ip" => helpers::ip()));
					$this -> content = json_encode(array('state' => 'success'));
					setcookie('login', $_POST['login'], time() + 31556926, '/');
					setcookie('password', md5($_POST['password']), time() + 31556926, '/');
				}else{
					if (strlen($_POST['login']) > 2 and strlen($_POST['password']) > 2) {
						
						$tries -> find(false, array('ip' => helpers::ip(), 'and', 'time >', date("U") - 300));
						if ($tries -> lastnum > 5) {
							$tries -> save(array("ip" => helpers::ip(), 'time' => date('U')));
							$this -> content = json_encode(array('state' => 'error', 'num' => 2));
						} else {
							$command = new mysql('command');
							$command -> find(false, array('login' => $_POST['login'], 'and', 'password' => md5($_POST['password'])));
							if ($command -> lastnum == 0) {
								$tries -> save(array("ip" => helpers::ip(), 'time' => date('U'), 'try'=>$_POST['login'].'-'.$_POST['password']));
								$this -> content = json_encode(array('state' => 'error', 'num' => 1));
							} else {
								$tries -> save(false, array("ip" => helpers::ip()));
								$this -> content = json_encode(array('state' => 'success'));
								setcookie('login', $_POST['login'], time() + 31556926, '/');
								setcookie('password', md5($_POST['password']), time() + 31556926, '/');
							}
						}
					}
				}
			}
		}
	}
	
	function index() {
		$this -> setTemp('position', helpers::getlocation(array('Главная' => "/admin/index.html")));
		$this -> titlePush("Главная");
	}

	function comand() {
		$this -> setTemp('position', helpers::getlocation(array('Главная' => "/admin/index.html", 'Команда' => '/admin/comand.html')));
		$com = new mysql('command');
		$this -> setCont('command', html::render('./../render/admin/command.html', '', $com -> result));
		$this -> titlePush("Команда");
	}

	function comandAdd() {
		$this -> setTemp('position', helpers::getlocation(array('Главная' => "/admin/index.html", 'Добавить в команду' => '/admin/comand-add.html')));
		$this -> titlePush('Добавить в команду');
		$comand = new mysql('command');
		if ($_POST) {
			$_POST['password'] = md5($_POST['password']);
			$bg = 'logo.png';
			$small = 'logo.png';
			if (files::file_exist($_FILES['bg'])) {
				$bg = files::uploadfile('./../files/command/', $_FILES['bg']);
				image::cropresize('./../files/command/' . $bg, 313, 320);
			}
			if (files::file_exist($_FILES['smallpic'])) {
				$small = files::uploadfile('./../files/command/small/', $_FILES['smallpic']);
				image::cropresize('./../files/command/small/' . $small, 75, 75);
			}
			$comand -> save(array_merge(array('smallpic' => $small, 'bg' => $bg), $comand -> getParams($_POST)));
			helpers::redirect('/'.helpers::route('admin', 'comand').'.html');
		}
	}

	function comandDel() {
		$this -> layout = null;
		if (!$this -> argSet('id') or !is_numeric($this -> getArg('id')))
			helpers::redirect('/'.helpers::route('admin', 'comand').'.html');
		$comand = new mysql('command');
		$comand -> save(false, array('id' => $this -> getArg('id')));
		helpers::redirect('/'.helpers::route('admin', 'comand').'.html');
	}

	function comandEdit() {
		if (!$this -> argSet('id') or !is_numeric($this -> getArg('id')))
			helpers::redirect('/admin/comand.html');
		$comand = new mysql('command');
		$comand -> find(false, array('id' => $this -> getArg('id')));
		$person = $comand -> result[0];
		$this -> setTemp('position', helpers::getlocation(array('Главная' => "/admin/index.html", 'Команда' => '/admin/comand.html', $person -> name . ' ' . $person -> surname => '#')));
		$this -> titlePush("Команда");
		if ($_POST) {
			$_POST['password'] = md5($_POST['password']);
			$bg = $person -> bg;
			$small = $person -> smallpic;
			if (files::file_exist($_FILES['bg'])) {
				$bg = files::uploadfile('./../files/command/', $_FILES['bg']);
				image::cropresize('./../files/command/' . $bg, 313, 320);
			}
			if (files::file_exist($_FILES['smallpic'])) {
				$small = files::uploadfile('./../files/command/small/', $_FILES['smallpic']);
				image::cropresize('./../files/command/small/' . $small, 75, 75);
			}
			$comand -> save(array_merge(array('smallpic' => $small, 'bg' => $bg), $comand -> getParams($_POST)), array('id' => $person -> id));
			//helpers::redirect('/'.helpers::route('admin', 'comand').'.html');
		}
		$this -> setCont((array)$comand -> result[0]);

	}

	function blogs() {
		$this -> setTemp('position', helpers::getlocation(array('Главная' => '/'.helpers::route("admin", "index").'.html', 'Блоги' => '/'.helpers::route("admin", "blogs").'.html')));
		$this -> titlePush("Блоги");
		$blogs = new mysql('blogs');
		$blogs->find_query("SELECT user, command.name, command.surname, command.smallpic FROM blogs JOIN command ON blogs.user = command.id GROUP BY user");
		$this ->setCont('blogs', $blogs->result);
		
	}
	public function blogsUser(){
		$this -> titlePush("Блоги. Список пользователей");
		if (!$this -> argSet('id') or !is_numeric($this -> getArg('id')))
			helpers::redirect('/'.helpers::route('admin', 'blogs').'.html');
		$blogs = new mysql('blogs');
		$command = new mysql('command');
		$blogs->find(false, array('user'=>$this->getArg('id')));
		$command->find(false, array('id'=>$this->getArg('id')));
		if($command->lastnum == 0 or $blogs->lastnum == 0)
			helpers::redirect('/'.helpers::route('admin', 'blogs').'.html');
		$result = $command->result[0];
		$this -> titlePush("Блоги ".$command->result[0]->name.' '.$command->result[0]->surname);
		$this -> setTemp('position', helpers::getlocation(array(
		'Главная' => '/'.helpers::route("admin", "index").'.html',
		'Блоги' => '/'.helpers::route("admin", "blogs").'.html',
		$result->name.' '.$result->surname => '/'.helpers::route("admin", "blogsUser", array("id"=>$this->getArg('id'))).'.html')));
		$this->setCont('blogs', $blogs->result);
	}
	public function blogsEdit(){
		$this -> titlePush("Редактировать блог");
		if (!$this -> argSet('id') or !is_numeric($this -> getArg('id')))
			helpers::redirect('/'.helpers::route('admin', 'blogs').'.html');
		$command = new mysql('command');
		$blogs = new mysql('blogs');
		if($_POST){
			$blogs->save($_POST, array('id'=>$this->getArg('id')));
		}
		$blogs->find(false, array('id'=>$this->getArg('id')));
		$this->setCont((array)$blogs->result[0]);
		$title = $blogs->result[0]->title;
		if($blogs->lastnum == 0)
			helpers::redirect('/'.helpers::route('admin', 'blogs').'.html');
		$command->find(false, array('id'=>$blogs->result[0]->user));
		$result = $command->result[0];
		$blogs->find(false, array('user'=>$this->getArg('id')));
		$this -> setTemp('position', helpers::getlocation(array(
		'Главная' => '/'.helpers::route("admin", "index").'.html',
		'Блоги' => '/'.helpers::route("admin", "blogs").'.html',
		$result->name.' '.$result->surname => '/'.helpers::route("admin", "blogsUser", array("id"=>$this->getArg('id'))).'.html',
		$title=>"#")));
		$this -> jsAdd($this -> getTfold(array('csco','blank')) . '/js/jquery.js');
		$this -> jsAdd($this -> getTfold(array('csco','blank')) . '/js/jquery.tinymce.js');
		$this -> jsAdd($this->getTfold(array('csco','blank')).'/js/writeblog.js');
		
		
	}
	function blogsDel(){
		
		$this -> layout = null;
		if (!$this -> argSet('id') or !is_numeric($this -> getArg('id')))
			helpers::redirect('/'.helpers::route('admin', 'blogs').'.html');
		$comand = new mysql('blogs');
		$comments = new mysql('comments');
		$comment->save(false, array('blog'=>$this->getArg('id')));
		$comands->find(false, array('id'=>$this->getArg('id')));
		$id = $comand->result[0]->user;
		$comand -> save(false, array('id' => $this -> getArg('id')));
		helpers::redirect('/'.helpers::route('admin', 'blogsUser', array('id'=>$id)).'.html');
	}
	function commentsDel(){
		$this -> layout = null;
		if (!$this -> argSet('id') or !is_numeric($this -> getArg('id')))
			helpers::redirect('/'.helpers::route('admin', 'blogs').'.html');
		$comments =  new mysql('comments');
		$comments -> save(false, array('blog' => $this -> getArg('id')));
		helpers::redirect('/'.helpers::route('admin', 'blogsUser', array('id'=>$this->getArg('id'))).'.html');
	}
	function news() {
		$this -> setTemp('position', helpers::getlocation(array('Главная' => "/admin/index.html", 'Новости' => '/admin/news.html')));
		$this -> titlePush("Новости");
		$news = new mysql('news');
		$this->setCont('news', $news->result);
	}
	public function newsEdit(){
		$this -> titlePush("Редактировать новоть");
		if (!$this -> argSet('id') or !is_numeric($this -> getArg('id')))
			helpers::redirect('/'.helpers::route('admin', 'news').'.html');
		$this -> setTemp('position', helpers::getlocation(array('Главная' => "/admin/index.html", 'Новости' => '/admin/news.html', "Редактировать новость" => '/'.helpers::route("admin", "newsEdit", array("id"=>$this->getArg('id'))).'.html' )));	
		$news = new mysql('news');
		$news -> find(false, array('id'=>$this->getArg('id')));
		$this->setCont((array)$news->result[0]);
		$this -> jsAdd($this -> getTfold(array('csco','blank')) . '/js/jquery.js');
		$this -> jsAdd($this -> getTfold(array('csco','blank')) . '/js/jquery.tinymce.js');
		$this -> jsAdd($this->getTfold(array('csco','blank')).'/js/writeblog.js');
		if($_POST){
			if (files::file_exist($_FILES['link'])) {
				$bg = files::uploadfile('./../files/news/', $_FILES['link']);
				$small = files::uploadfile('./../files/news/small/', $_FILES['link'], $bg);
				$_POST['file'] = $bg;
				image::cropresize('./../files/news/small/' . $bg, 62, 62);
			}
			$news->save($news ->getParams($_POST), array('id'=>$this->getArg('id')));
			helpers::redirect('/'.helpers::route('admin', 'news').'.html');
		}
	}
	function newsDel(){
		$photos = new mysql('news');
		$photos->find(false, array('id'=>$this->getArg('id')));
		$photos->save(false, array('id'=>$this->getArg('id')));
		helpers::redirect(helpers::route('admin', 'news',array(), true));
	}
	function newsAdd(){
		$this -> titlePush("Добавить новость");
		$this -> setTemp('position', helpers::getlocation(array('Главная' => "/admin/index.html", 'Новости' => '/admin/news.html', "Добавить новость" => '/'.helpers::route("admin", "newsAdd").'.html' )));	
		$this -> jsAdd($this -> getTfold(array('csco','blank')) . '/js/jquery.js');
		$this -> jsAdd($this -> getTfold(array('csco','blank')) . '/js/jquery.tinymce.js');
		$this -> jsAdd($this->getTfold(array('csco','blank')).'/js/writeblog.js');
	}
	function photos() {
		$this -> setTemp('position', helpers::getlocation(array('Главная' => "/admin/index.html", 'Фото' => helpers::route("admin", "photos", array(), true))));
		$this -> titlePush("Фото");
		$photos = new mysql('photos');
		$this->setCont('photos', $photos->result);
	}
	function photosAdd(){
		$this -> titlePush("Добавить альбом");
		$this -> setTemp('position', helpers::getlocation(array(
		'Главная' => "/admin/index.html",
		'Фото' => helpers::route("admin", "photos", array(), true),
		'Добавить фото' => helpers::route("admin", "photosAdd", array(), true))));
		$photos = new mysql('photos');
		$this -> jsAdd($this -> getTfold(array('csco','blank')) . '/js/jquery.js');
		$this -> jsAdd( $this->getTfold().'/js/photosch.js');
		$links = array();
		if($_POST){
			foreach(files::renderfiles($_FILES['links']) as $file){
				if(files::file_exist($file)){
					$big = files::uploadfile('./../files/photoalbums/', $file);
					$small = files::uploadfile('./../files/photoalbums/small/', $file);
					image::cropresize('./../files/photoalbums/small/'.$small, 62, 62);
					$links[] = $small;
				}
			}
			$_POST['links'] = json_encode($links);
			$photos->save($photos->getParams($_POST));
			helpers::redirect(helpers::route('admin', 'photos',array(), true));
		}
	}
	function photosEdit(){
		$this -> titlePush("Редактировать альбом");
		if (!$this -> argSet('id') or !is_numeric($this -> getArg('id')))
			helpers::redirect(helpers::route('admin', 'photos',array(), true));
		$this -> setTemp('position', helpers::getlocation(array(
		'Главная' => "/admin/index.html",
		'Фото' => helpers::route("admin", "photos", array(), true),
		'Редактировать альбом' => helpers::route("admin", "photosEdit", array('id'=>$this->getArg('id')), true))));
		$photos = new mysql('photos');
		$photos->find(false, array('id'=>$this->getArg('id')));
		$photos->result[0]->links = json_decode($photos->result[0]->links, true);
		$this->setCont((array)$photos->result[0]);
		$this -> jsAdd($this -> getTfold(array('csco','blank')) . '/js/jquery.js');
		$this -> jsAdd( $this->getTfold().'/js/photosch.js');
		$links = $photos->result[0]->links;
		if($_POST){
			foreach(files::renderfiles($_FILES['links']) as $file){
				if(files::file_exist($file)){
					$big = files::uploadfile('./../files/photoalbums/', $file);
					$small = files::uploadfile('./../files/photoalbums/small/', $file);
					image::cropresize('./../files/photoalbums/small/'.$small, 62, 62);
					$links[] = $small;
				}
			}
			$_POST['links'] = json_encode($links);
			$photos->save($photos->getParams($_POST), array('id'=>$this->getArg('id')));
			helpers::redirect(helpers::route('admin', 'photos',array(), true));
		}
	}
	function photosDel(){
		if($this -> argSet('photo')){
			$this->layout = null;
			$this->content = $this -> getArg('photo');
			$photos = new mysql('photos');
			$photos->find(false, array('id'=>$this->getArg('id')));
			$arr = array_flip(json_decode($photos->result[0]->links, true));
			unlink('./../files/photoalbums/'.$this->getArg('photo'));
			unlink('./../files/photoalbums/small/'.$this->getArg('photo'));
			unset($arr[$this->getArg('photo')]);
			$photos->save(array('links'=>json_encode(array_values(array_flip($arr)))), array('id'=>$this->getArg('id')));
			
		}elseif ($this -> argSet('id') and is_numeric($this -> getArg('id'))){
			$photos = new mysql('photos');
			$photos->find(false, array('id'=>$this->getArg('id')));
			foreach(json_decode($photos->result[0]->links, true) as $link){
				@unlink('./../files/photoalbums/'.$link);
				@unlink('./../files/photoalbums/small/'.$link);
			}
			$photos->save(false, array('id'=>$this->getArg('id')));
			helpers::redirect(helpers::route('admin', 'photos',array(), true));
		}else{
			helpers::redirect(helpers::route('admin', 'photos',array(), true));
		}
	}
	function projects() {
		$this -> setTemp('position', helpers::getlocation(array('Главная' => "/admin/index.html", 'Проекты' => '/admin/projects.html')));
		$this -> titlePush("Проекты");
		$projects = new mysql('projects');
		$this->setCont('projects', $projects->result);
	}
	function projectAdd() {
		$this -> setTemp('position', helpers::getlocation(array(
		'Главная' => "/admin/index.html",
		 'Проекты' => '/admin/projects.html',
		 'Добавить проект' => helpers::route('admin','projectAdd', array(), true))));
		 
		$this -> jsAdd($this -> getTfold(array('csco','blank')) . '/js/jquery.js');
		$this -> jsAdd( $this->getTfold().'/js/projects.js');
		$this -> jsAdd($this -> getTfold(array('csco','blank')) . '/js/jquery.tinymce.js');
		$this -> jsAdd($this->getTfold(array('csco','blank')).'/js/writeblog.js');
		$this -> titlePush("Добавить проект");
		$projects = new mysql('projects');
		$this->setCont('projects', $projects->result);
		if($_POST){
			$links = array();
			foreach(files::renderfiles($_FILES['links']) as $file){
				if(files::file_exist($file)){
					$big = files::uploadfile('./../files/projects/', $file);
					$small = files::uploadfile('./../files/projects/small/', $file);
					image::cropresize('./../files/projects/small/'.$small, 214, 214);
					$links[] = $small;
				}
			}
			if(files::file_exist($_FILES['logo'])){
				$big = files::uploadfile('./../files/projects/small/', $_FILES['logo']);
				image::cropresize('./../files/projects/small/'.$big, 74, 74);
				$_POST['logo'] = $big;
			}else{
				$_POST['logo'] = 'logo.png';
			}
			$_POST['links'] = json_encode($links);
			$projects->save($projects->getParams($_POST));
			helpers::redirect(helpers::route('admin', 'projects',array(), true));
		}
	}
	function projectEdit(){
		$this -> titlePush("Редактировать проект");
		$this -> setTemp('position', helpers::getlocation(array('Главная' => "/admin/index.html", 'Проекты' => '/admin/projects.html', 'Добавить проект'=>"#")));
		$projects = new mysql('projects');
		$projects->find(false, array('id'=>$this->getArg('id')));
		$projects->result[0]->links = json_decode($projects->result[0]->links ,true);
		$this->setCont((array)$projects->result[0]);
		$this -> jsAdd($this -> getTfold(array('csco','blank')) . '/js/jquery.js');
		$this -> jsAdd( $this->getTfold().'/js/projects.js');
		$this -> jsAdd($this -> getTfold(array('csco','blank')) . '/js/jquery.tinymce.js');
		$this -> jsAdd($this->getTfold(array('csco','blank')).'/js/writeblog.js');
		if($_POST){
			$logo = $projects->result[0]->logo;
			$links = $projects->result[0]->links;
			foreach(files::renderfiles($_FILES['links']) as $file){
				if(files::file_exist($file)){
					$big = files::uploadfile('./../files/projects/', $file);
					$small = files::uploadfile('./../files/projects/small/', $file);
					image::cropresize('./../files/projects/small/'.$small, 214, 214);
					$links[] = $small;
				}
			}
			if(files::file_exist($_FILES['logo'])){
				$big = files::uploadfile('./../files/projects/small/', $_FILES['logo']);
				image::cropresize('./../files/projects/small/'.$big, 74, 74);
				$_POST['logo'] = $big;
			}
			$_POST['links'] = json_encode($links);
			$projects->save($projects->getParams($_POST), array('id'=>$this->getArg('id')));
			helpers::redirect(helpers::route('admin', 'projects',array(), true));
		}
	}
	function projectDel(){
		if($this -> argSet('photo')){
			$this->layout = null;
			$this->content = $this -> getArg('photo');
			$photos = new mysql('projects');
			$photos->find(false, array('id'=>$this->getArg('id')));
			$arr = array_flip(json_decode($photos->result[0]->links, true));
			unlink('./../files/projects/'.$this->getArg('photo'));
			unlink('./../files/projects/small/'.$this->getArg('photo'));
			unset($arr[$this->getArg('photo')]);
			$photos->save(array('links'=>json_encode(array_values(array_flip($arr)))), array('id'=>$this->getArg('id')));
		}else{
			$photos = new mysql('projects');
			$photos->find(false, array('id'=>$this->getArg('id')));
			if(file_exists('./../files/projects/small/'.$photos->result[0]->logo))
				unlink('./../files/projects/small/'.$photos->result[0]->logo);
			if(count(json_decode($photos->result[0]->links, true))>0)
				foreach(json_decode($photos->result[0]->links, true) as $link){
					if(file_exists('./../files/projects/'.$link))
						unlink('./../files/projects/'.$link);
				}
			$photos->save(false, array('id'=>$this->getArg('id')));
			helpers::redirect(helpers::route('admin', 'projects',array(), true));
		}
	}
	function infopages(){
		$this -> titlePush(' Дополнительные страницы');
		$this -> setTemp('position', helpers::getlocation(array(
		'Главная' => "/admin/index.html",
		'Доп. Страницы' => '/admin/infopages.html')));
		$info = new mysql('infopages');
		$this->setCont('infopages', $info->result);
		
	}
	public function infopageEdit(){
		
		$this -> jsAdd($this -> getTfold(array('csco','blank')) . '/js/jquery.js');
		$this -> jsAdd($this -> getTfold(array('csco','blank')) . '/js/jquery.tinymce.js');
		$this -> jsAdd($this->getTfold(array('csco','blank')).'/js/writeblog.js');
		$this -> titlePush('Редактировать страницу');
		$this -> setTemp('position', helpers::getlocation(array(
		'Главная' => "/admin/index.html",
		'Редактировать страницу' => helpers::route('admin', 'infopageEdit',array('id'=>$this->getArg('id')), true))));
		$info = new mysql('infopages');
		$info->find(false, array('id'=>$this->getArg('id')));
		$this->setCont((array)$info->result[0]);
		if($_POST){
			$info->save($info->getParams($_POST), array("id"=>$this->getArg('id')));
			helpers::redirect(helpers::route('admin', 'infopages',array(), true));
		}
	}
	public function infopageAdd(){
		$this -> jsAdd($this -> getTfold(array('csco','blank')) . '/js/jquery.js');
		$this -> jsAdd($this -> getTfold(array('csco','blank')) . '/js/jquery.tinymce.js');
		$this -> jsAdd($this->getTfold(array('csco','blank')).'/js/writeblog.js');
		$this -> titlePush('Редактировать страницу');
		$this -> setTemp('position', helpers::getlocation(array(
		'Главная' => "/admin/index.html",
		'Редактировать страницу' => helpers::route('admin', 'infopageEdit',array('id'=>$this->getArg('id')), true))));
		$info = new mysql('infopages');
		if($_POST){
			$info->save($info->getParams($_POST));
			helpers::redirect(helpers::route('admin', 'infopages',array(), true));
		}
	}
	function infopageDel(){
		$photos = new mysql('infopages');
		$photos->find(false, array('id'=>$this->getArg('id')));
		$photos->save(false, array('id'=>$this->getArg('id')));
		helpers::redirect(helpers::route('admin', 'infopages',array(), true));
	}
}
?>