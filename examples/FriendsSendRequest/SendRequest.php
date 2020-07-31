<?php  
require "../../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookFriendsSendRequest;

$cookie = 'cookie';

$url = "https://mbasic.facebook.com/a/mobile/friends/profile_add_friend.php?subjectid=100005801619164&istimeline=1&hf=profile_button&fref=gs&frefid=0&referrer_uri=https%3A%2F%2Fwww.facebook.com%2Fgroups%2Fj2team.community.girls%2Fmembers%2F&gfid=AQDRkwLZV7JJsuIZ";

$send = new FacebookFriendsSendRequest();
$send->Required([
	'cookie' => $cookie,
	'useragent' => false, //  false for auto genereate
	'proxy' => false // false for not use proxy 
]);

$process = $send->Process($url);

echo "<pre>";
var_dump($process);
echo "</pre>";

/*
array(2) {
  ["status"]=>
  bool(true)
  ["response"]=>
  string(15) "success_request"
}
*/