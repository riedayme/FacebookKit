<?php  
require "../../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookPostReactionSend;

$cookie = 'cookie';

$postid = '3079795912133643';
$reaction = 'SAD'; // LIKE, LOVE, CARE, HAHA, WOW, SAD, ANGRY, UNREACT

$send = new FacebookPostReactionSend();
$send->Required([
	'cookie' => $cookie,
	'useragent' => false, //  false for auto genereate
	'proxy' => false // false for not use proxy 
]);

$results =$send->Process($postid,$reaction);

echo "<pre>";
var_dump($results);
echo "</pre>";

/*
array(2) {
  ["status"]=>
  bool(true)
  ["response"]=>
  string(13) "success_react"
}
*/