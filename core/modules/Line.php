<?php
class line {
	static function plusmounth($m, $y, $p) {
		$m = $m + $p;
		while ($m > 12) {
			$m = $m - 12;
			$y++;
		}
		return array($m, $y);
	}

	static function getline($mounth, $year, $side) {
		if (($mounth < 2 and $year <= 2012) or ($mounth > (int)date("m") and $year >= (int)date('Y')) or !is_numeric($mounth) or !is_numeric($year))
			return "";
		$return = "";
		$genblock = array();
		$trigg = false;
		for ($i = 1; $i <= self::inmounth($mounth, $year); $i++) {
			$genblock[] = self::genblock($i, $mounth, $year, $side);
			if ($genblock[$i - 1] !== "") {
				$trigg = true;
				$side = $side ? 0 : 1;
			}
		}
		$return .= '<div class="month">' . $mounth . '</div>';
		$add = !$trigg ? " style='display:none'" : "";
		$return .= '<div class="days_wrapper"' . $add . '>';
		for ($i = 1; $i <= self::inmounth($mounth, $year); $i++) {
			$active = ($genblock[$i - 1]=="")?"":" active_day";
			$return .= '<div class="day'.$active.'">' . $i . $genblock[$i - 1] . '</div>';
		}
		$return .= '</div>';
		//return $return;
		return json_encode(array($return, $side));
	}

	static function inmounth($mounth, $year) {
		if (in_array($mounth, array(1, 3, 5, 7, 8, 10, 12)))
			return 31;
		elseif (in_array($mounth, array(4, 6, 9, 11)))
			return 30;
		elseif ($mounth == 2)
			return self::yearst($year) ? 29 : 28;
	}

	static function yearst($year) {
		return (($year % 4 == 0 or $year % 400 == 0) and $year % 100 !== 0) ? true : false;
	}

	static function genblock($d, $m, $y, $side) {
		$news = new mysql('news');
		$photoss = new mysql('photos');
		$projects = new mysql("projects");
		$news -> find(false, array("date" => implode('-', array($y, $m, $d))));
		$photoss -> find(false, array("date" => implode('-', array($y, $m, $d))));
		$projects -> find(false, array("date" => implode('-', array($y, $m, $d))));
		$nn = "active_block";
		$mr = "block";
		$result = "";
		$pixx = 0;
		if ($photoss -> lastnum > 0) {
			$links = html::genClass("url", json_decode($photoss -> result[0] -> links, true));

			$photos = html::render('', '<img class="resizable_image" src="/files/photoalbums/small/%url%">', $links);
			$result .= html::compile("./../render/photoline.html", "", array('moreread'=>$mr,'active' => $nn, "photos" => $photos, 'title' => $photoss -> result[0] -> name));
			$nn = "";
			$mr = "none";
			$pixx = 394;
		}
		if ($news -> lastnum > 0) {
			$results = (array)$news -> result[0];
			$results['active'] = $nn;
			$results['moreread'] = $mr;
			$result .= html::compile("./../render/newsline.html", "", $results);
			$nn = "";
			$mr = "none";
			$pixx = !$pixx?394:$pixx+27;
		}
		if ($projects -> lastnum > 0) {
			$results = (array)$projects -> result[0];
			$results['active'] = $nn;
			$results['moreread'] = $mr;
			$result .= html::compile("./../render/projectsline.html", "", $results);
			$nn = "";
			$mr = "none";
			$pixx = !$pixx?394:$pixx+27;
		}
		$ss = $side ? "left" : "right";
		if ($result !== "")
		$result = html::compile("./../render/block.html", "", array("side" => $ss, 'width'=>$pixx,"elements" => $result));
		return $result;
	}

}
?>