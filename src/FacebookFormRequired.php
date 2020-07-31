<?php namespace Riedayme\FacebookKit;


Class FacebookFormRequired
{

	public static function Login() 
	{

		$url = 'https://m.facebook.com/login/';

		$headers = array();
		$headers[] = 'User-Agent: '.FacebookUserAgent::GenerateStatic();

		$access = FacebookHelper::curl($url,false,$headers);

		$response = $access['body'];

		$pattern_input = '/<input.*?name="(.*?)".*?value="(.*?)".*?>/';
		preg_match_all($pattern_input, $response, $matches);

		if (empty($matches)) {
			die("Tidak dapat mendapatkan Input Value");
		}

		$params = array();
		foreach ($matches[1] as $index => $key) {
			$params[$key] = $matches[2][$index];
		}


		$cookie = FacebookCookie::ReadCookie($access['header']);

		return [
			'input' => $params,
			'cookie' => $cookie
		];
	}

	public static function GetFbDTSG($data) 
	{

		$url = 'https://mbasic.facebook.com/profile.php';

		$headers = array();
		$headers[] = 'User-Agent: '.$data->useragent;
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Cookie: '.$data->cookie;

		$access = FacebookHelper::curl($url,false,$headers);

		$response = $access['body'];

		$pattern_input = '/<input.*?name="(.*?)".*?value="(.*?)".*?>/';
		preg_match_all($pattern_input, $response, $matches);

		if (empty($matches)) {
			return [
				'status' => false,
				'response' => 'not_found_required'
			];
		}elseif ($access['code'] == '404') {
			return [
				'status' => false,
				'response' => 'not_login'
			];
		}

		$params = array();
		foreach ($matches[1] as $index => $key) {
			$params[$key] = $matches[2][$index];
		}

		if (array_key_exists('fb_dtsg', $params)) {
			return [
				'status' => true,
				'response' => $params['fb_dtsg']
			];
		}else{
			return [
				'status' => false,
				'response' => 'not_found'
			];
		}

	}	

	public static function SendRequestFriendship($data) 
	{

		$url = $data['url'];

		$headers = array();
		$headers[] = 'User-Agent: '.$data['useragent'];
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Cookie: '.$data['cookie'];	

		$access = FacebookHelper::curl($url,false,$headers);

		$response = $access['body'];

		$pattern_input = '/<input.*?name="(.*?)".*?value="(.*?)".*?>/';
		preg_match_all($pattern_input, $response, $matches);

		if (empty($matches)) {
			die("Tidak dapat mendapatkan Input Value");
		}

		$params = array();
		foreach ($matches[1] as $index => $key) {
			$params[$key] = $matches[2][$index];
		}

		return $params;
	}	
}
