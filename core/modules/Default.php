<?
class DefaultC {
	function __construct($core) {
		$this -> controller = $core -> controller;
		$this -> routes = $core -> routes;
		$this -> constructor();
	}

	function contructor() {
		
	}

	function getTfold() {
		$layout = $this -> controller -> layout;
		if (is_array($layout)) {
			unset($layout[count($layout) - 1]);
			$tfold = "/templates/" . implode("/", $layout);
		} elseif (is_string($layout)) {
			$tfold = "/templates/default/";
		} else
			$tfold = "";
		return $tfold;
	}

	protected function setTemp($var) {
		if (is_array($var)) {
			foreach ($var as $ket => $v) {
				$this -> controller->  templatevars[$ket] = $v;
			}
		} else {
			if (func_num_args() > 1) {
				$this -> controller->  templatevars[$var] = func_get_arg(1);
			}
		}
	}

	protected function setCont($var) {
		if (is_array($var)) {
			foreach ($var as $ket => $v) {
				$this -> controller-> contentvars[$ket] = $v;
			}
		} else {
			if (func_num_args() > 1) {
				$this -> controller->  contentvars[$var] = func_get_arg(1);
			}
		}
	}

}
?>