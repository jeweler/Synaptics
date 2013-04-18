<?php
/**
 * The Module Class.
 *
 * Даёт набор методов для работой с MYSQL
 *
 * @package DB
 */
class Module {
	var $con, $table, $error, $rows, $result = array(), $querystack = array();
	function __construct($table) {
		if(class_exists('YAML')){
			$conn = YAML::YAMLLoad('configs/config.yaml');
			$mysql_conn = $conn['mysql_conn'];
		}else{
			include 'config.php';
		}
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
	public function getpage($page = 1, $perpage = 10){
		if($perpage > 1 and $page > 1){	
			$pages = ceil(count($result)/$perpage);
			$page = ($page > $pages)? 1 : $page;
			return array_slice($this->result, ($page-1)*$perpage, $perpage);
		}
		return array();
	}
	private function safe($string = "", $key=false) {
		if($key){
			if(isset($this->rows[$key])){
				if(is_string($string) or is_numeric($string)){
					$string = mysql_real_escape_string($string);
					$type = $this->rows[$key]['Type'];
					$length = $this->rows[$key]['Length'];
				
					if($type !== 'int' and $type !== 'bigint'){
						$string = '\''.$string.'\'';
					}
					return $string;
				}elseif(is_array($string)){
					$string = '\''.json_encode($string).'\'';
					return $string;
				}else{
					return null;
				}
			}else{
				return null;
			}
		}else{
			return mysql_real_escape_string($string);
		}
	}
	/**
	 * Поиск записей в таблице
	 * @access public
	 * @return array/null
	 * @param array $rows поля, которые нужно вернуть 
	 * @param array $where условие поиска Array("id"=>5, "and", "someparam"=>array(">", 5));
	 * @param array $join формат array('join_table', 'join_row', 'table_row')
	 * @param array $order формат array("date", "DESC");
	 * @param array $limit формат array(5, 30);
	 */
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
			if(is_array($order)){
				if (count($order) == 2) {
					$query = array_merge($query, array('ORDER BY', $order[0], $order[1]));
				}
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
	/**
	 * Удаляет из таблицы все записи найденные с помощью метода find
	 * @access public
	 * @return null
	 */
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
	/**
	 * Изменяет записи, найденные с помощью метода find
	 * @access public
	 * @param array $params поля, которые нужно изменить
	 * @return null
	 */
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
	/**
	 * Возвращает дату в формате для MYSQL 'Y-m-d h:i:s'
	 * @access public
	 * @param string $date Дата, которую нужно преобразовать в формат для  MYSQL
	 * @return string
	 */
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
	/**
	 * Добавляет новую запись в таблицу
	 * @access public
	 * @param array $params Значения полей новой записи array('title'=>'sometitle', 'date'=>'1994-11-18')
	 * @return string
	 */
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
	/**
	 * Возвращает все записи в таблице по запросу
	 * @access public
	 * @param string $query SQL запрос
	 * @return array
	 */
	function getByQuery($query){
		$query = mysql_query($query, $this->con);
		$return = array();
		if($query){
			if(mysql_num_rows($query)>0){
				while($result = mysql_fetch_array($query)){
					foreach($result as $key=>$value)
						$result[$key] = (json_decode($value) == null)?$value:json_decode($value);
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