<?php
class image {
	static function resize($file_input, $file_output, $w_o, $h_o, $percent = false) {
		list($w_i, $h_i, $type) = getimagesize($file_input);
		if (!$w_i || !$h_i) {
			echo 'Невозможно получить длину и ширину изображения';
			return;
		}
		$types = array('', 'gif', 'jpeg', 'png');
		$ext = $types[$type];
		if ($ext) {
			$func = 'imagecreatefrom' . $ext;
			$img = $func($file_input);
		} else {
			echo 'Некорректный формат файла';
			return;
		}
		if ($percent) {
			$w_o *= $w_i / 100;
			$h_o *= $h_i / 100;
		}
		if (!$h_o)
			$h_o = $w_o / ($w_i / $h_i);
		if (!$w_o)
			$w_o = $h_o / ($h_i / $w_i);

		$img_o = imagecreatetruecolor($w_o, $h_o);
		imagecopyresampled($img_o, $img, 0, 0, 0, 0, $w_o, $h_o, $w_i, $h_i);
		if ($type == 2) {
			return imagejpeg($img_o, $file_output, 100);
		} else {
			$func = 'image' . $ext;
			return $func($img_o, $file_output);
		}
	}

	static function crop($file_input, $file_output, $crop = 'square', $percent = false) {
		list($w_i, $h_i, $type) = getimagesize($file_input);
		if (!$w_i || !$h_i) {
			echo 'Невозможно получить длину и ширину изображения';
			return;
		}
		$types = array('', 'gif', 'jpeg', 'png');
		$ext = $types[$type];
		if ($ext) {
			$func = 'imagecreatefrom' . $ext;
			$img = $func($file_input);
		} else {
			echo 'Некорректный формат файла';
			return;
		}
		if ($crop == 'square') {
			$min = $w_i;
			if ($w_i > $h_i)
				$min = $h_i;
			$w_o = $h_o = $min;
			$x_o = 0;
			$y_o = 0;
		} else {
			list($x_o, $y_o, $w_o, $h_o) = $crop;
			if ($percent) {
				$w_o *= $w_i / 100;
				$h_o *= $h_i / 100;
				$x_o *= $w_i / 100;
				$y_o *= $h_i / 100;
			}
			if ($w_o < 0)
				$w_o += $w_i;
			$w_o -= $x_o;
			if ($h_o < 0)
				$h_o += $h_i;
			$h_o -= $y_o;
		}
		$img_o = imagecreatetruecolor($w_o, $h_o);
		imagecopy($img_o, $img, 0, 0, $x_o, $y_o, $w_o, $h_o);
		if ($type == 2) {
			return imagejpeg($img_o, $file_output, 100);
		} else {
			$func = 'image' . $ext;
			return $func($img_o, $file_output);
		}
	}
	static function cropresize($input, $w, $h){
		list($w1, $h1) = getimagesize($input);
		if($w1 > $h1){
			self::resize($input, $input, 0, $w);
		}else{
			self::resize($input, $input, $h, 0);
		}
		self::crop($input, $input, array(0,0, $w,$h));
	}

}
?>