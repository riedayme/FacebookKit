<?php namespace Riedayme\FacebookKit;

class FacebookAccessToken
{

	public static function GetTouchToken($cookie)
	{

		$url = 'https://m.facebook.com/composer/ocelot/async_loader/?publisher=feed';
		$headers = array();
		$headers[] = 'User-Agent: '.FacebookUserAgent::GenerateStatic();
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Cookie: '.$cookie;		

		$access = FacebookHelper::curl($url,false,$headers);

		$response = $access['body'];

		$accesstoken = FacebookHelper::GetStringBetween($response,'accessToken\":\"','\",\"useLocalFilePreview\"');

		return $accesstoken;
	}

}