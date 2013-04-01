<?
class IndexController extends Controller {
	public function index() {
		$conf = new Module("configs");
		$conf ->add(array("param"=>array(1,2,3), "value"=>1));
	}
}
?>