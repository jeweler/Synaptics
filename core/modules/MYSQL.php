<?
class Module {
	var $con, $table, $error, $rows, $result = array(), $querystack = array();
	function __construct($table) {
		include 'config.php';
		$this -> con = mysql_connect($mysql_conn['host'] . ':' . $mysql_conn['port'], $mysql_conn['user'], $mysql_conn['password']);
		mysql_select_db($mysql_conn['db'], $this -> con);
		$result = mysql_query("SHOW TABLES LIKE '" . $table . "'");
		if (mysql_num_rows($result) == 0)
			new Except(new Exception("Не найдена таблица " . $table));
		mysql_query('SET NAMES utf8', $this -> con);
		$this -> table = $table;
		$query = mysql_query('SHOW COLUMNS FROM ' . $this -> table);
		while ($row = mysql_fetch_assoc($query)) {
			$this -> rows[$row["Field"]] = $row;
			preg_match_all("/^(\w+)(\(([0-9]+)\))?$/", $row["Type"], $params);
			$this -> rows[$row["Field"]]['Type'] = $params[1][0];
			$this -> rows[$row["Field"]]['Length'] = isset($params[3][0])?$params[3][0]:null;
		}
	}
	function safe($string = "", $key=false) {
		if($key){
			if(isset($this->rows[$key])){
				$string = mysql_real_escape_string($string);
				$type = $this->rows[$key]['Type'];
				$length = $this->rows[$key]['Length'];
				if($type !== 'int' and $type !== 'bigint'){
					$string = '\''.$string.'\'';
				}
				
				return $string;
			}else{
				return null;
			}
		}else{
			return mysql_real_escape_string($string);
		}
	}
	
	function find($rows = false, $where = false, $join = false, $order = false, $limit = false) {
		$query[] = 'SELECT';
		if ($rows) {
			if (is_array($rows)) {
				foreach ($rows as $key => $as) {
					if (is_string($key)) {
						$query = array_merge($query, array('(', $key, ') as', is_numeric($as) ? '\'' . $as . '\'' : $as, ','));
					} else {
						$query = array_merge($query, array('(', $as, ')', ','));
					}
				}
				unset($query[count($query) - 1]);
			} elseif (is_string($rows)) {
				$query = array_merge($query, array('(', $rows, ')'));
			}
		} else {
			$query[] = '*';
		}
		$query[] = "FROM";
		$query[] = $this -> table;
		if ($where) {
			if (count($where) > 0) {
				$query[] = 'WHERE';
				foreach ($where as $key => $value) {
					if (is_numeric($key)) {
						$query[] = $value;

					} elseif (is_array($value)) {

						if (count($value) == 2) {
							if (key_exists($key, $this -> rows)) {
								if($this->safe($value[1], $key) !== null){
									$query[] = $key;
									$query[] = $value[0];
									$query[] = $this->safe($value[1], $key);
								}
							}
						}
					} else {
						if (key_exists($key, $this -> rows)) {
							if($this->safe($value, $key) !== null){
								$query[] = $key;
								$query[] = '=';
								$query[] = $this->safe($value, $key);
							}
						}
					}
				}
			}
		}
		if($query[count($query)-1] == "WHERE") unset($query[count($query)-1]);
		if ($join) {
			if (is_array($join)) {
				if (count($join) == 3) {
					$query = array_merge($query, array('JOIN', $join[0], 'ON', $join[0] . '.' . $join[1], '=', $this -> table . '.' . $join[2]));
				}
			}
		}
		if ($order) {
			if ($order) {
				$query = array_merge($query, array('ORDER BY', $order[0], $order[1]));
			}
		}
		if ($limit) {
			if (is_array($limit)) {
				if (count($limit) == 2) {
					$query = array_merge($query, array('LIMIT', $limit[0], ',', $limit[1]));
				}
			}
		}
		$this -> query = implode($query, " ");
		$this->querystack []= $this->query;
		return $this->getByQuery($this->query);
	}
	function delete(){
		if(count($this->result) > 0){
			$query = "DELETE FROM ".$this->table.' WHERE id IN (';
			$ids = array();
			foreach($this->result as $one){
				$ids[]=$one->id;
			}
			$query .= implode($ids,',').')';
			$this->query = $query;
			$this->querystack []= $query;
			mysql_query($query, $this->con);
		}
	}
	function update($params = array()){
		$query = array();
		$query []= 'UPDATE';
		$query []= $this->table;
		$query []='SET';
		$paramsq = array();
		if(count($params)>0){
			foreach($params as $key=>$param){
				if($this->safe($param, $key)){
					$paramsq []= $key.' = '. $this->safe($param, $key);
				}
			}
			$query []= implode($paramsq, ', ');
		}
		if($query[count($query)-1] == 'SET')
			new Except(new Exception("Ошибка запроса!"));
		$query[] = "WHERE id IN (";
		$ids = array();
			foreach($this->result as $one){
				$ids[]=$one->id;
			}
			
		if(count($ids) > 0){
			$query []= implode($ids,',').' )';
			$this->query = implode(' ', $query);
			$this->querystack []= implode(' ', $query);
			mysql_query($this->query, $this->con);
		}
	}
	static function date($date = ""){
		if($date == "")
			return date('Y-m-d h:i:s');
		else{
			$ym = explode(' ', $date);
			$ymd = $ym[0];
			$tme = isset($ym[1])?$ym[1]:'';
			$d = preg_match_all('/\W/', $ymd, $res);
			$date = explode($res[0][0],$ymd);
			if(count($date) == 3){
				if(strlen($date[0]) == 4){
					$ret = $date[0].'-'.$date[1].'-'.$date[2];
				}else{
					$ret = $date[2].'-'.$date[1].'-'.$date[0];
				}
			}else{
				$ret = date('Y-m-d');
			}
			if(strlen($tme)>0){
				$d = preg_match_all('/\W/', $tme, $res);
				if($d){
					$time = explode($res[0][0], $tme);
					if(count($time) == 3){
						$ret .= ' '.$time[0].':'.$time[1].':'.$time[2];
					}else{
						$ret .= date(' h:i:s');
					}
				}else{
					$ret .= date(' h:i:s');
				}
			}else{
				$ret .= date(' h:i:s');
			}
			return $ret;
			
		}
	}
	function add($params){
		if(count($params)>0){
			$query = array();
			$query []= 'INSERT INTO';
			$query []= $this->table;
			$query []= '(';
			$_s = array();
			if(count($params) > 0){
				foreach($params as $row=>$val){
					if(isset($this->rows[$row]))
						if($this->safe($val, $row))
							$_s[] = $row;
				}
				$query []= implode($_s, ' , ');
			}
			if(count($_s) > 0){
				$query []= ') VALUES (';
				$_s = array();
				foreach($params as $row=>$val)
					if(isset($this->rows[$row]))
						if($this->safe($val, $row))
							$_s[] = $this->safe($val, $row);
				$query []= implode($_s, ' , ');
				$query []= ')';
				$query = implode($query, ' ');
				$this->query = $query;
				$this->querystack []= $query;
				mysql_query($query, $this->con);
				$this->error = mysql_errno($this->con).'::'.mysql_error($this->con);
				$this->find(false, array('id'=>mysql_insert_id($this->con)));
				return mysql_insert_id($this->con);
			}else{
				new Except(new Exception('Не указаны параметры добавления'));
			}
		}
	}
	function getByQuery($query){
		$query = mysql_query($query, $this->con);
		$return = array();
		if($query){
			if(mysql_num_rows($query)>0){
				while($result = mysql_fetch_array($query)){
					foreach($result as $key=>$value)
						if(json_decode($value))
					$return []= (object)$result;
				}
				$this->result = $return;
				return $return;
			}else{
				return array();
			}
		}else{
			new Except(new Exception('Ошибка запроса sql:'.mysql_errno($this->con).'::'.mysql_error($this->con)));
			$this->error = mysql_errno($this->con).'::'.mysql_error($this->con);
			$this->result = null;
			$this->query = $query;
			return array();
		}
	}

}
?>