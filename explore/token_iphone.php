<?php
/***
***			Facebook Android Access_Token:ShareFBScripts
***				Copyright (c) 2016 @ ShareFBScripts.BlogSpot.Com
***						ShareFBScripts
**/

error_reporting(E_ALL);

header('Origin: https://facebook.com');
define('API_SECRET', 'c1e620fa708a1d5696fb991c1bde5662');
define('BASE_URL', 'https://api.facebook.com/restserver.php');

function sign_creator(&$data){
	$sig = "";
	foreach($data as $key => $value){
		$sig .= "$key=$value";
	}
	$sig .= API_SECRET;
	$sig = md5($sig);
	return $data['sig'] = $sig;
}

function cURL($method = 'GET', $url = false, $data){
	//sign_creator($data);
	//print_r($data);
	$c = curl_init();
	$user_agents = array(
		"Mozilla/5.0 (iPhone; CPU iPhone OS 9_2_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Mobile/13D15 Safari Line/5.9.5",
		"Mozilla/5.0 (iPhone; CPU iPhone OS 9_0_2 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Mobile/13A452 Safari/601.1.46 Sleipnir/4.2.2m","Mozilla/5.0 (iPhone; CPU iPhone OS 9_3 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13E199 Safari/601.1","Mozilla/5.0 (iPod; CPU iPhone OS 9_2_1 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) CriOS/45.0.2454.89 Mobile/13D15 Safari/600.1.4","Mozilla/5.0 (iPhone; CPU iPhone OS 9_3 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13E198 Safari/601.1"
		);
	$useragent = $user_agents[array_rand($user_agents)];
	$opts = array(
		CURLOPT_URL => ($url ? $url : BASE_URL).($method == 'GET' ? '?'.http_build_query($data) : ''),
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_USERAGENT => $useragent
		);
	if($method == 'POST'){
		$opts[CURLOPT_POST] = true;
		$opts[CURLOPT_POSTFIELDS] = $data;
	}
	curl_setopt_array($c, $opts);
	$d = curl_exec($c);
	curl_close($c);
	return $d;
}

if(isset($_POST['u'], $_POST['p'])){
	$_GET = $_POST;
}
$data = array(
	"api_key" => "3e7c78e35a76a9299309885393b02d97",
	"credentials_type" => "password",
	"email" => @$_GET['u'],
	"format" => "JSON",
	"generate_machine_id" => "1",
	"generate_session_cookies" => "1",
	"locale" => "en_US",
	"method" => "auth.login",
	"password" => @$_GET['p'],
	"return_ssl_resources" => "0",
	"v" => "1.0"
	);
sign_creator($data);
$response = cURL('GET', false, $data);
$responseJSON = json_decode($response);

if (@$responseJSON->error_msg) {
	exit($responseJSON->error_msg);
}else {
	exit($responseJSON->access_token);
}

?>