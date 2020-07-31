<?php namespace Riedayme\FacebookKit;

class FacebookChecker
{

	public static function CheckLiveCookie($cookie)
	{

		$url = 'https://mbasic.facebook.com/';
		$headers = array();
		$headers[] = 'User-Agent: '.FacebookUserAgent::GenerateStatic();
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Cookie: '.$cookie;		

		$access = FacebookHelper::curl($url,false,$headers);

		$response = $access['body'];

		if (!strstr($response, 'mbasic_logout_button')) {
			return [
				'status' => false,
				'response' => 'Cookie Die'
			];		
		}

		return [
			'status' => true,
			'response' => 'active'
		];
	}

}