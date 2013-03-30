<?php
class BlogsController extends Controller {
	function constructor() {
		$this -> setTitle("Constant Solutions: ");
		$this -> layout = array("csco", "default");
	}

	function index() {
		$this -> cssAdd($this -> getTfold() . '/css/blogs.css');
		$this -> titlePush("Блоги");
		$blogs = new mysql('blogs');
		$blogs -> find_query("SELECT user, command.name, command.surname, command.smallpic FROM blogs JOIN command ON blogs.user = command.id GROUP BY user");
		$this -> setCont('blogs', (array)$blogs -> result);
		if($blogs->lastnum == 0){
			helpers::redirect('/' . helpers::route('index', 'index') . '.html');
		}
	}

	public function user() {
		$this -> cssAdd($this -> getTfold() . '/css/userblogs.css');
		if (!$this -> argSet('id') or !is_numeric($this -> getArg('id')))
			helpers::redirect('/' . helpers::route('blogs', 'index') . '.html');
		$blogs = new mysql('blogs');
		$blogs -> find(false, array('user' => $this -> getArg('id')));
		$command = new mysql('command');
		$command -> find(false, array('id' => $this -> getArg('id')));
		$this -> titlePush('Блоги ' . $command -> result[0] -> name . ' ' . $command -> result[0] -> surname);
		if ($blogs -> lastnum == 0)
			helpers::redirect('/' . helpers::route('blogs', 'index') . '.html');
		$this -> setCont('blogs', (array)$blogs -> result);
	}

	public function writeblog() {
		if(helpers::authorized()){
			$this -> jsAdd($this -> getTfold() . '/js/writeblog.js');
				$this -> jsAdd($this -> getTfold() . '/js/jquery.tinymce.js');
				$this -> cssAdd($this -> getTfold() . '/css/writeblog.css');
				if($_POST){
					$blogs = new mysql('blogs');
					$vars = $blogs->getParams($_POST);
					$vars['date'] = date("Y-m-d H:i:s");
					$vars['user'] = helpers::authorized();
					$blogs->save($vars);
				}
		}else{
			helpers::redirect('/' . helpers::route('index', 'index') . '.html');
		}
	}

	public function allblogs() {
		$this -> titlePush('Все блоги');
		$this -> cssAdd($this -> getTfold() . '/css/userblogs.css');
		$blogs = new mysql('blogs');
		$this -> setCont('blogs', (array)$blogs -> result);
	}

	public function read() {
		$this -> cssAdd($this -> getTfold() . '/css/readblog.css');
		if (!$this -> argSet('id') or !is_numeric($this -> getArg('id')))
			helpers::redirect('/' . helpers::route('blogs', 'index') . '.html');
		$blogs = new mysql('blogs');
		$blogs -> find(false, array('id' => $this -> getArg('id')));
		$this -> titlePush('Блог: "' . $blogs -> result[0] -> title . '"');
		if ($blogs -> lastnum == 0)
			helpers::redirect('/' . helpers::route('blogs', 'index') . '.html');
		$this -> setCont((array)$blogs -> result[0]);
		$comments = new mysql('comments');
		if ($_POST) {
			$save = $comments -> getParams($_POST);
			$save['blog'] = $this -> getArg('id');
			$save['date'] = date("Y-m-d H:i:s");
			$comments -> save($save);
		}
		$comments -> find(false, array('blog' => $this -> getArg('id')));
		$this -> setCont('comments', $comments -> result);
	}

}
?>