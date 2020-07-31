<?php namespace Riedayme\FacebookKit;

class FacebookFriendsRequestConfirm
{

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

	public function Process($url)
	{

		$headers = array();
		$headers[] = 'User-Agent: '.$this->useragent;
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Cookie: '.$this->cookie;	

		$access = FacebookHelper::curl($url, false , $headers , false, false, $this->proxy);

		$response = $access['header'];

		if (strstr($response, 'login.php')) {
			return [
				'status' => false,
				'response' => 'not_login'
			];
		}else{			
			if (strstr($response, '200 OK') AND strstr($response, '302 Found') OR strstr($response, 'HTTP/2 200') AND strstr($response, 'HTTP/2 302')) {
				return [
					'status' => TRUE,
					'response' => 'success_confirm'
				];
			}else{
				return [
					'status' => false,
					'response' => 'failed_confirm'
				];
			}
		}
	}
}