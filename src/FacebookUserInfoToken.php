<?php namespace Riedayme\FacebookKit;

class FacebookUserInfoToken
{


	Const URL = 'https://graph.facebook.com/me?fields=name,picture&access_token=%s';

	public $access_token;
	public $useragent;		
	public $proxy;

	public function Required($data) 
	{

		if (!$data['access_token']) {
			die('access_token Empty');
		}

		$this->access_token = $data['access_token'];

		if (!$data['useragent']) {
			$this->useragent = FacebookUserAgent::GenerateStatic();
		}else{
			$this->useragent = $data['useragent'];
		}

		if (!$data['proxy']) {
			$this->proxy = false;
		}else{
			$this->proxy = $data['proxy'];
		}

	}

	public function Process()
	{

		$url = sprintf(self::URL,$this->access_token);

		$headers = array();
		$headers[] = 'User-Agent: '.$this->useragent;

		$access = FacebookHelper::curl($url, false , $headers , false, false, $this->proxy);

		$response = json_decode($access['body'],true);


		if (array_key_exists('error', $response)) {
			return [
				'status' => false,
				'response' => $response['error']['message']
			];
		}else{
			return [
				'status' => true,
				'response' => [
					'id' => $response['id'],
					'username' => $response['name'],
					'photo' => $response['picture']['data']['url']
				]
			];
		}
	}

}