<?php namespace Riedayme\FacebookKit;

class FacebookAuthCookie
{

	public static function Login($cookie)
	{

		$check_cookie = FacebookChecker::CheckLiveCookie($cookie);

		if (!$check_cookie['status']) {
			return [
				'status' => false,
				'response' => $check_cookie['response']
			];
		}

		$userid = FacebookCookie::GetUIDCookie($cookie);

		$access_token = FacebookAccessToken::GetTouchToken($cookie);

		$readuserinfo = new FacebookUserInfoToken();
		$readuserinfo->Required([
			'access_token' => $access_token,
			'useragent' => false, //  false for auto genereate
			'proxy' => false // false for not use proxy 
		]);

		$userinfo = $readuserinfo->Process();

		if (!$userinfo['status']) {
			return [
				'status' => false,
				'response' => $userinfo['response']
			];
		}

		$userinfo = $userinfo['response'];

		return [
			'status' => true,
			'response' => [
				'userid' => $userid,
				'username' => $userinfo['username'], 
				'photo' => $userinfo['photo'],
				'cookie' => $cookie,
				'access_token' => $access_token
			]
		];

	}

}