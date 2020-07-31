<?php  
require "../../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookUserGroups;

$cookie = 'cookie';

$Group = new FacebookUserGroups();
$Group->Required([
	'cookie' => $cookie,
	'useragent' => false, //  false for auto genereate
	'proxy' => false // false for not use proxy 
]);

$results =$Group->Process();

echo "<pre>";
var_dump($results);
echo "</pre>";

/*
array(1) {
  [0]=>
  array(3) {
    ["id"]=>
    string(15) "250732861682621"
    ["name"]=>
    string(17) "Blogger Indonesia"
    ["url"]=>
    string(43) "https://facebook.com/groups/250732861682621"
  }
}
*/