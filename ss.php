<?
$perpage = 5;
$page = 2;
if($perpage > 1 and $page > 1){	
			$pages = ceil(count($result)/$perpage);
			$page = ($page > $pages)? 1 : $page;
			var_dump(array_slice($this->result, ($page-1)*$perpage, $perpage));
}
?>