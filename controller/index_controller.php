<?
class IndexController extends Controller {
	public function index() {
		$this -> layout = array("sati", 'index');
		$this -> setTitle("Сатисфакция: Главная");
		$this -> jsAdd($this -> getTfold() . '/js/jquery.js');
		$this -> jsAdd($this -> getTfold() . '/js/index.js');
	}

	function news() {
		$this -> layout = array("sati", 'about');
		$this -> setTitle("Сатисфакция: Новости");
		$this -> cssAdd($this -> getTfold() . '/css/news.css');
		$this -> jsAdd($this -> getTfold() . '/js/jquery.js');
		$this -> jsAdd($this -> getTfold() . '/js/about.js');
		$this -> setTemp("back", "0290");
		$news = new mysql('news');
		$this -> content = "%render(info/news, news)%";
		$this -> setCont('news', $news -> result);
	}
	
	function tryi(){
		$m = new Module('press');
		$m -> add(array("title"=>'5'));
		$m -> update(array('photo'=>'cool', 'time'=>Module::date()));
		var_dump($m->querystack);
	}
	function history() {
		$this -> layout = array("sati", 'about');
		$this -> setTitle("Сатисфакция: Новости");
		$this -> cssAdd($this -> getTfold() . '/css/history.css');
		$this -> jsAdd($this -> getTfold() . '/js/jquery.js');
		$this -> jsAdd($this -> getTfold() . '/js/about.js');
		$this -> setTemp("back", "0291");
		$conf = new mysql('configs');
		$conf -> find(false, array('param' => 'history'));
		$this -> content = $conf -> result[0] -> value;
	}

	function members() {
		$this -> layout = array("sati", 'about');
		$this -> setTitle("Сатисфакция: Новости");
		$this -> cssAdd($this -> getTfold() . '/css/persons.css');
		$this -> jsAdd($this -> getTfold() . '/js/jquery.js');
		$this -> jsAdd($this -> getTfold() . '/js/about.js');
		$this -> setTemp("back", "0292");
		$pers = new mysql('persons');
		$this -> setCont('persons', $pers -> result);
		$this -> content = "%render(info/persons, persons)%";
	}

	function press() {
		$this -> layout = array("sati", 'about');
		$this -> setTitle("Сатисфакция: Пресса");
		$this -> cssAdd($this -> getTfold() . '/css/press.css');
		$this -> jsAdd($this -> getTfold() . '/js/jquery.js');
		$this -> jsAdd($this -> getTfold() . '/js/about.js');
		$this -> setTemp("back", "0293");
		$press = new mysql('press');
		$press -> find(false, false, 'ORDER BY  `time` DESC ');
		$this -> setCont('press', $press -> result);
		$this -> content = "%render(info/press, press)%";
	}

	function readPress() {
		$this -> layout = array("sati", 'about');
		$this -> setTitle("Сатисфакция: Пресса");
		$this -> cssAdd($this -> getTfold() . '/css/readpress.css');
		$this -> jsAdd($this -> getTfold() . '/js/jquery.js');
		$this -> jsAdd($this -> getTfold() . '/js/about.js');
		$this -> setTemp("back", "0293");
		$press = new mysql('press');
		$press ->find(false, array('english'=>$this->getArg('id')));
		$this -> setCont((array)$press -> result[0]);
	}

	function audio() {
		$this -> setTitle("Сатисфакция: Аудио");
		$this -> layout = array("sati", 'multimedia');
		$this -> jsAdd($this -> getTfold() . '/js/jquery.js');
		$this -> jsAdd($this -> getTfold() . '/js/multimedia.js');
		$this -> setTemp("back", "0294");
	}

	function video() {
		$this -> setTitle("Сатисфакция: Видео");
		$this -> layout = array("sati", 'multimedia');
		$this -> jsAdd($this -> getTfold() . '/js/jquery.js');
		$this -> jsAdd($this -> getTfold() . '/js/multimedia.js');
		$this -> setTemp("back", "0295");
	}

	function texts() {
		$this -> setTitle("Сатисфакция: Тексты");
		$this -> layout = array("sati", 'multimedia');
		$this -> jsAdd($this -> getTfold() . '/js/jquery.js');
		$this -> jsAdd($this -> getTfold() . '/js/multimedia.js');
		$this -> setTemp("back", "0297");
	}

	function photos() {
		$this -> setTitle("Сатисфакция: Фото");
		$this -> layout = array("sati", 'multimedia');
		$this -> jsAdd($this -> getTfold() . '/js/jquery.js');
		$this -> jsAdd($this -> getTfold() . '/js/multimedia.js');
		$this -> setTemp("back", "0296");
	}

}
?>