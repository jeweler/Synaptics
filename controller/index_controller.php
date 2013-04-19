<?php
class IndexController extends Controller {
	public function index() {
		$this->layout = "index";
	}
	public function second_page(){
		$this->layout = array("charisma", "blank");
		$this->setTitle("Это заголовок");
		$this->content = "Это контент";
	}
}
?>