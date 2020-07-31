<?php  
require "../../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookUserInfoToken;

$access_token = 'youraccesstoken';

$read = new FacebookUserInfoToken();

$read->Required([
	'access_token' => $access_token,
	'useragent' => false, //  false for auto genereate
	'proxy' => false // false for not use proxy 
]);

$results = $read->Process();

echo "<pre>";
var_dump($results);
echo "</pre>";

/*
array(2) {
  ["status"]=>
  bool(true)
  ["response"]=>
  array(3) {
    ["id"]=>
    string(15) "100016865703374"
    ["username"]=>
    string(8) "Riedayme"
    ["photo"]=>
    string(236) "https://scontent-sin6-2.xx.fbcdn.net/v/t1.0-1/cp0/p50x50/65307879_471865026719017_5286366118670237696_o.jpg?_nc_cat=108&_nc_sid=dbb9e7&_nc_ohc=sCN1ZviLWKIAX9PK-9V&_nc_ht=scontent-sin6-2.xx&oh=438f7302b8b3bc4a3060fd2992fd36fe&oe=5F4B3496"
  }
}
*/