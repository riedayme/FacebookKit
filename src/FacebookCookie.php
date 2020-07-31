<?php namespace Riedayme\FacebookKit;

Class FacebookCookie
{

	public static function ReadCookie($response) 
	{

		preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $results);
		$cookies = '';
		for($o = 0; $o < count($results[0]); $o++){
			$cookies.=$results[1][$o].";";
		}

		if (!$cookies) return false;

		return $cookies;
	}

	public static function GetUIDCookie($cookies){
		$cookies_to_arr = explode(';', $cookies);
		$result = FacebookHelper::FindStringOnArray($cookies_to_arr, 'c_user');
		if (count($result) > 1) {
			$result = array_slice($result, 1);
		}
		$result_userid = implode("", $result);
		$userid = substr(trim($result_userid), 7);

		return $userid;
	}
}
