<?php  
require "../../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookFriendsRequestConfirm;

$cookie = 'cookie';

$url = "url";

$SendRequest = new FacebookFriendsRequestConfirm();
$SendRequest->Required([
	'cookie' => $cookie,
	'useragent' => false, //  false for auto genereate
	'proxy' => false // false for not use proxy 
]);

$process = $SendRequest->Process($url);

echo "<pre>";
var_dump($process);
echo "</pre>";