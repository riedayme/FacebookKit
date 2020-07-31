<?php namespace Riedayme\FacebookKit;

class FacebookPostDelete
{

	Const URL = 'https://mbasic.facebook.com/delete.php?perm&story_permalink_token=S:_I%s:%s';
	Const URLDELETE = 'https://mbasic.facebook.com/a/delete.php?perm&story_permalink_token=S:_I%s:%s';

	public $cookie;
	public $useragent;		
	public $proxy;

	public function Required($data) 
	{

		if (!$data['cookie']) 
		{
			die('cookie Empty');
		}

		$this->cookie = $data['cookie'];

		if (!$data['useragent']) 
		{
			$this->useragent = FacebookUserAgent::GenerateStatic();
		}else{
			$this->useragent = $data['useragent'];
		}

		if (!$data['proxy']) 
		{
			$this->proxy = false;
		}else{
			$this->proxy = $data['proxy'];
		}
	}	

	public function Process($userid,$postid)
	{

		$formdelete = self::GetFormDelete($userid,$postid);

		$url = sprintf(self::URLDELETE,$userid,$postid);

		$headers = array();
		$headers[] = 'User-Agent: '.$this->useragent;
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Cookie: '.$this->cookie;

		$postdata['fb_dtsg'] = $formdelete['fb_dtsg'];
		$postdata['jazoest'] = $formdelete['jazoest'];
		$postdata = http_build_query($postdata);

		$access = FacebookHelper::curl($url, $postdata , $headers , false, false, $this->proxy);

		$response = $access['header'];

		if (strstr($response, 'login.php')) 
		{
			return [
				'status' => false,
				'response' => 'not_login'
			];
		}else{

			if (strstr($response, '200 OK') AND strstr($response, '302 Found') OR strstr($response, 'HTTP/2 200') AND strstr($response, 'HTTP/2 302')) {
				return [
					'status' => true,
					'response' => 'success_delete'
				];
			}else{
				return [
					'status' => false,
					'response' => 'failed_delete'
				];
			}

		}

	}

	public function GetFormDelete($userid,$postid)
	{

		$url = sprintf(self::URL,$userid,$postid);

		$headers = array();
		$headers[] = 'User-Agent: '.$this->useragent;
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Cookie: '.$this->cookie;

		$access = FacebookHelper::curl($url, false , $headers , false, false, $this->proxy);

		$response = $access['body'];

		$pattern_input = '/<input.*?name="(.*?)".*?value="(.*?)".*?>/';
		preg_match_all($pattern_input, $response, $matches);

		if (empty($matches)) {
			die("Tidak dapat mendapatkan tag Input");
		}

		$params = array();
		foreach ($matches[1] as $index => $key) {
			$params[$key] = $matches[2][$index];
		}

		return $params;
	}	

}