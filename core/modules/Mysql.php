<?
class mysql {
	var $lastnum;
	var $table;
	var $result;
	var $con;
	var $lasterror;
	var $lastquery;
	var $db;
	var $metaprefix;
	function __construct($table) {
		$config = array();
		include ('./config.php');
		$this -> con = mysql_connect(@$mysql_conn['host'].':'.@$mysql_conn['port'], @$mysql_conn['user'], @$mysql_conn['password']) or die('Ошибка базы : ' . mysql_error());
		$this -> db = $mysql_conn['db'];
		$this -> metaprefix = $mysql_conn["meta"];
		mysql_select_db(@$mysql_conn['db'], $this -> con);
		mysql_query("SET NAMES utf8", $this -> con);
		$this -> table = $table;
		$this->find();
	}
	function getpage($params=false, $cond=false, $add="", $meta=FALSE, $perpage=0, $pagenum=0){
		$perpage=(int)$perpage;
		$pagenum=(int)$pagenum;
		if($perpage<1){
			echo "Ошибка с выводом!";
			die();
		}else{
			if($pagenum<1) $pagenum=1;
			
			if($pagenum>$this->getpages($perpage)) $pagenum=1;
			$curpos=($pagenum-1)*$perpage;
			$this->find($params,$cond, $add." LIMIT $curpos, $perpage", $meta);
		}
	}
	function getpages($perpage = 0) {
		$perpage=(int)$perpage;
		if ($perpage < 1) {
			echo "Ошибка! Введите по сколько будете выводить!";
		}else{
			$this->find();
			$num=ceil($this->lastnum/$perpage);
			return $num;
		}
	}
	function getParams($array){
		$ret = array();
		$cols = $this->columns();
		
		foreach($array as $key=>$value){
			if(in_array($key, $cols))
				$ret[$key]=$value;
		}
		return $ret;
	}
	function gen_cond($cond) {
		$query = '';
		if (is_array($cond)) {
			$first = 'false';
			foreach ($cond as $key => $condi) {
				if ($condi !== 'or' and $condi !== 'and' and !is_numeric($key)) {
					if (!is_array($condi)) {
						if ($first !== "TRUE")
							$query .= ' WHERE';
						$first = 'TRUE';
						$query .= ' `' . $key . '` = ';
						$query .= (is_numeric($condi)) ? $condi : "'" . $this -> inj($condi) . "'";
					} else {
						if ($first !== "TRUE")
							$query .= isset($condi[3]) ? $condi[3] : ' and';
						else
							$query .= ' WHERE';
						$first = 'TRUE';
						$query .= ' `' . $condi[0] . '` ' . $condi[1] . ' ';
						$query .= (is_numeric($condi[2])) ? $condi[2] : "'" . $this -> inj($condi[2]) . "'";
					}
				} else {
					$query .= ' ' . $condi;
				}
			}
		}
		return $query;
	}
	function columns(){
		$ret = array();
		$query = mysql_query("SHOW COLUMNS FROM ".$this->table);
		while($a = mysql_fetch_array($query)){
			$ret []= $a['Field'];	
		}
		return $ret;
	}
	function find($params = '*', $cond = False, $add = false, $meta = false) {
		$this->result = array();
		if (is_array($params)) {
			$para = '';
			$i = 0;
			foreach ($params as $param) {
				$para .= '' . $param . "";
				if ($i !== count($params) - 1)
					$para .= ', ';
				$i++;
			}
			$query = trim("SELECT " . $para . " FROM " . $this -> table);
		} elseif (is_string($params) and $params !== "*") {
			$query = trim("SELECT " . $params . " FROM " . $this -> table);
		} else {
			$query = trim("SELECT *" . " FROM " . $this -> table);
		}
		$query .= $this -> gen_cond($cond);
		if ($add) {
			if (is_string($add)) {
				$query .= " " . $add;
			} elseif (is_array(add)) {
				foreach ($add as $ad) {
					$query .= " " . $ad;
				}
			}
		}
		$this -> lastquery = $query;
		$query = mysql_query($query, $this -> con) or new Except(new Exception("Ошибка запроса!"));
		$this -> lastnum = @mysql_num_rows($query);
		if ($query and mysql_num_rows($query) > 0) {
			while ($values = mysql_fetch_assoc($query)) {
				if ($meta) {
					$metatable = new ModuleRecord($this -> table . $this -> metaprefix);

					$vars = $metatable -> find('*', array('id_' . $this -> table => $values['id']));
					if ($metatable -> lastnum > 0) {
						foreach ($metatable->result as $var) {
							$var = (array)$var;
							$values[$var['key']] = $var['value'];
						}
					}
				}
				$rec[] = (object)$values;
			}
			$this -> result = $rec;
			return $rec;
		} elseif (!$query) {
			return false;
		} elseif (mysql_num_rows($query) == 0) {
			return false;
		}

	}
	function find_query($query){
		$query= mysql_query($query, $this->con);
		$res = array();
		if(mysql_num_rows($query)>0){
			while($res = mysql_fetch_array($query)){
				$ress[] = (object)$res;
			}
			$this->result = $ress;
			$this->lastnum = count($res);
			$this->lastquery = $query;
			return $ress;
		}else{
			$this->result = array();
			$this->lastnum = 0;
			$this->lastquery = $query;
			return array();
		}
	}
	private function query($query) {
		$this -> lastquery = $query;
		if (mysql_query($query, $this -> con)) {
			$this -> lastid = mysql_insert_id();
			return TRUE;
		} else {

			$this -> lasterror = mysql_errno();
			return false;
		}
	}

	private function inj($string) {
		$string = mysql_real_escape_string($string);
		return (trim($string));
	}

	function putmeta($id, $key, $val) {
		$metatable = new ZverRecord($this -> table . $this -> metaprefix);
		$metatable -> find(false, array('id_' . $this -> table => $id, 'and', 'key' => $key));
		if (count($metatable -> result) > 0) {
			$metatable -> save(array('value' => $val), array('key' => $key, 'and', 'id_' . $this -> table => $id));
		} else {
			$metatable -> save(array('id_' . $this -> table => $id, 'key' => $key, 'value' => $val));
		}

	}

	function save($params = false, $cond = false, $meta = false) {
		$error = true;
		$ermes = "";
		if (is_array($params)) {
			foreach ($params as $key => $con) {
				if (isset($this -> check[$this -> table][$key])) {
					$condit = $this -> check[$this -> table][$key]['check'];
					$mesif = $this -> check[$this -> table][$key]['mes'];

					if ($condit == "numeric") {
						if (!is_numeric($con)) {
							$ermes .= $mesif . "<br>";
							$error = false;
						}
					}
					if ($condit == "preg") {
						$preg = $this -> check[$this -> table][$key]['preg'];
						$match = preg_match($preg, $con);
						if (!$match) {
							$ermes .= $mesif . "<br>";
							$error = false;
						}
					}
					if ($condit == "email") {
						$preg = "/^(.*?)@(.*?)\.(.*)$/";
						$match = preg_match($preg, $con);
						if (!$match) {
							$ermes .= $mesif . "<br>";
							$error = false;
						}
					}

				}
			}
		}
		if (is_array($cond) and is_array($params)) {
			$query = "UPDATE `" . $this -> table . "` SET";
			$n = 1;
			foreach ($params as $key => $con) {
				$query .= " `" . $key . "`=";
				$query .= is_numeric($con) ? $con : '\'' . $this -> inj($con) . '\'';
				if ($n !== count($params))
					$query .= ',';
				$n++;

			}
			$query .= $this -> gen_cond($cond);
			if ($error) {
				return $this -> query($query);
			} else {
				$this -> lasterror = $ermes;
				return false;
			}

		} elseif (is_array($params) and !is_array($cond)) {
			$query = "INSERT INTO `" . $this -> table . "`";
			$keys = ' (';
			$vals = ' (';
			$n = 1;
			foreach ($params as $key => $param) {
				$keys .= "`" . $key . "`";
				$vals .= is_numeric($param) ? $param : "'" . $this -> inj($param) . "'";
				if ($n !== count($params)) {
					$vals .= ',';
					$keys .= ',';
				}
				$n++;
			}
			$keys .= ")";
			$vals .= ")";
			$query .= $keys . ' VALUES' . $vals;
			if ($error) {
				return $this -> query($query);
			} else {
				$this -> lasterror = $ermes;
				return false;
			}
			if (is_array($meta)) {
				foreach ($meta as $key => $data) {
					$this -> putmeta($this -> lastid, $key, $data);
				}
			}
		} elseif (is_array($cond) and !is_array($params)) {
			$query = "DELETE FROM `" . $this -> table . "`" . $this -> gen_cond($cond);
			$this -> lastquery = $query;
			$this -> query($query);
		}
	}

}
?>