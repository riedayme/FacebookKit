<?php  
require "../../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookAuth;

$username = 'username';
$password = 'password';

$auth = new FacebookAuth();
$results =$auth->AuthLoginByMobile($username,$password);

echo "<pre>";
var_dump($results);
echo "</pre>";

/*
array(5) {
  ["userid"]=>
  string(11) "16865703374"
  ["username"]=>
  string(8) "Riedayme"
  ["photo"]=>
  string(289) "https://scontent.fcgk18-1.fna.fbcdn.net/v/t1.0-1/cp0/p50x50/65307879_471865026719017_5286366118670237696_o.jpg?_nc_cat=108&_nc_sid=dbb9e7&_nc_oc=AQkA1tPKTEJv0_g_qwuLt2-8CYg6KoEw8XzBH3TxvTIYT4M6IXpRX_ejowVsHnZVx8M&_nc_ht=scontent.fcgk18-1.fna&oh=c36377221c3dde5c3e3edfce982af731&oe=5F07F816"
  ["cookie"]=>
  string(190) "fr=19XJ4W59Zcs9g2Rv6.AWV8-QoRGgtF0vMu3S8jv7vinok.Be5DhB.WZ.AAA.0.0.Be5DhB.AWV7eQWa;sb=QTjkXjcvPTHfvDhcz3UQThNT;c_user=100016865703374;xs=14%3A94mQcA2k3O0Fgw%3A2%3A1592014913%3A17482%3A10881;"
  ["access_token"]=>
  string(182) "EAAAAZAw4FxQIBAEhzt284KS6POfnFbRUgYBfJmlZAAtpJE8XAlbGEY1lBXZBigmjZBxitMeDKYh3DZCogx28WZCLJ0pFaRZAG2My09nfGtZC23LG5TPTjJYD5ZAIyZApDM0qZCcH51VuNAmsxpiXHhaY52t1TJinaqu5BiBp12MOnBHPAZDZD"
}
*/