<?php
/**
***		Script Refresh token IOS
***			by: ShareFBScripts.BlogSpot.Com
***				Copyright (c) 2016. ShareFBScripts
**/

error_reporting(E_ALL);

header('Origin: https://facebook.com');
$user = $_POST['u'];
$pass = $_POST['p'];

$cnf = array(
	'email' => $user,
	'pass' =>  $pass
	);


//Login
$cnf['login'] = 'Login';
$random = md5(rand(00000000,99999999)).'.txt';
$login = cURL('https://m.facebook.com/login.php', $random, $cnf);
//print $login;
if(preg_match('/name="fb_dtsg" value="(.*?)"/', $login, $response)){
	$fb_dtsg = $response[1];
	$responseToken = cURL('https://www.facebook.com/v1.0/dialog/oauth/confirm', $random, 'fb_dtsg='.$fb_dtsg.'&app_id=165907476854626&redirect_uri=fbconnect://success&display=popup&access_token=&sdk=&from_post=1&private=&tos=&login=&read=&write=&extended=&social_confirm=&confirm=&seen_scopes=&auth_type=&auth_token=&auth_nonce=&default_audience=&ref=Default&return_format=access_token&domain=&sso_device=ios&__CONFIRM__=1');
	if(preg_match('/access_token=(.*?)&/', $responseToken, $token2)){
		$token = $token2[1];
		exit($token);
	}else{
		$token = 'Please allow Location Must ..';
		exit($token);
	}
}else{
	$token = 'Email or Password is Invalid..';
	exit($token);
}
function cURL($url, $cookie = false, $PostFields = false){
	$c = curl_init();
	$opts = array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_FRESH_CONNECT => true,
		CURLOPT_USERAGENT => 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_2_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Mobile/13D15 Safari Line/5.9.5',
		CURLOPT_FOLLOWLOCATION => true
		);
	if($PostFields){
		$opts[CURLOPT_POST] = true;
		$opts[CURLOPT_POSTFIELDS] = $PostFields;
	}
	if($cookie){
		$opts[CURLOPT_COOKIE] = true;
		$opts[CURLOPT_COOKIEJAR] = $cookie;
		$opts[CURLOPT_COOKIEFILE] = $cookie;
	}
	curl_setopt_array($c, $opts);
	$data = curl_exec($c);
	curl_close($c);
	return $data;
}
// unlink(@$random);
?>