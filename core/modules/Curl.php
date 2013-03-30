<?
	class curl{
		static $result, $error;
		private static function	gench($params){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
			curl_setopt($ch, CURLOPT_COOKIEJAR, "/coo.txt");
       		curl_setopt($ch, CURLOPT_COOKIEFILE,"/coo.txt");
       		curl_setopt($ch, CURLOPT_TIMEOUT, 90000);
			curl_setopt($ch, CURLOPT_ENCODING, "");
      		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			if(in_array('headers', $params)) curl_setopt($ch, CURLOPT_HEADER, 1);
			return $ch;
		}
		static function get($url,$params=array()){
			$curl = curl::gench($params);
			curl_setopt($curl, CURLOPT_URL, $url);
			self::$result = curl_exec($curl);
			self::$error = curl_error($curl);
			return self::$result;
		}
		static function post($url,$vars,$params=array()){
			$curl = curl::gench($params);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			self::$result = curl_exec($curl);
			self::$error = curl_error($curl);
			return self::$result;
		}
		
	}
?>