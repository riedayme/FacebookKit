<?php  
require "../../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookPostDelete;

$cookie = 'cookie';

$userid = '100016865703374'; // riedayme
$postid = '736328776939306';

$send = new FacebookPostDelete();
$send->Required([
	'cookie' => $cookie,
	'useragent' => false, //  false for auto genereate
	'proxy' => false // false for not use proxy 
]);

$results =$send->Process($userid,$postid);

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