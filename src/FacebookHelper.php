<?php namespace Riedayme\FacebookKit;

Class FacebookHelper
{

	public static function curl($url, $postdata = 0, $header = 0, $cookie = 0, $useragent = 0, $proxy = array()) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		curl_setopt($ch, CURLOPT_HEADER, 1);

		if($header) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_ENCODING, "gzip");
		}

		if($postdata) {
			curl_setopt($ch, CURLOPT_POST, 1);
			if ($postdata != 'empty') {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
			}
		}

		if($cookie) {
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
		}

		if ($useragent) {
			curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
		}

		if (!empty($proxy['proxy']['ip'])){
			curl_setopt($ch, CURLOPT_PROXY, $proxy['proxy']['ip']);
		}

		if (!empty($proxy['proxy']['userpwd'])){
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy['proxy']['userpwd']);
		}

		if (!empty($proxy['proxy']['socks5'])){
			curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
		}

		$response = curl_exec($ch);

		$httpcode = curl_getinfo($ch);

		if(!$httpcode) {
			curl_close($ch);	
			die("Response header not found"); 
		}
		else{

			$header = substr($response, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
			$body = substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));

			curl_close($ch);

			return [
				'header' => $header,
				'body' => $body,
				'code' => $httpcode['http_code']
			];
		}
	}	

	public static function FindStringOnArray($arr, $string) {
		return array_filter($arr, function($value) use ($string) {
			return strstr($value, $string) !== false;
		});
	}

	public static function GetStringBetween($string,$start,$end){
		$str = explode($start,$string);
		if (empty($str[1])) return false;
		$str = explode($end,$str[1]);
		return $str[0];
	}

	public static function innerHTML($node)
	{
		$doc = new \DOMDocument();
		foreach ($node->childNodes as $child) 
		{
			$doc->appendChild($doc->importNode($child, true));
		}
		return $doc->saveHTML();
	}	

	public static function GetDom($html)
	{

		$previous_value = libxml_use_internal_errors(TRUE);
		$dom = new \DOMDocument;
		$dom->loadHTML($html);
		libxml_clear_errors();
		libxml_use_internal_errors($previous_value);

		return $dom;
	}

	public static function GetXpath($dom)
	{

		$xpath = new \DOMXPath($dom);
		return $xpath;
	}

}