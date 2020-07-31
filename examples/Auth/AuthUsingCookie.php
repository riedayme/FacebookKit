<?php  
require "../../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookAuth;

$cookie = 'yourcookie';

$auth = new FacebookAuth();

$results =$auth->AuthUsingCookie($cookie);

echo "<pre>";
var_dump($results);
echo "</pre>";

/*
array(4) {
  ["userid"]=>
  string(11) "16865703374"
  ["username"]=>
  string(8) "Riedayme"
  ["photo"]=>
  string(289) "https://scontent.fcgk18-2.fna.fbcdn.net/v/t1.0-1/cp0/p50x50/65307879_471865026719017_5286366118670237696_o.jpg?_nc_cat=108&_nc_sid=dbb9e7&_nc_oc=AQlR44uQTTGpK1P-JvbTdwWtKME8zaWoQmCU-uMKN2sDW-UDr43-_YHstt96SJgiDis&_nc_ht=scontent.fcgk18-2.fna&oh=829b6253a2ac6c99cd9d813972ef572c&oe=5F040396"
  ["cookie"]=>
  string(475) "sb=IddBXhX0VL6oJlj52JyiTuvJ; datr=IddBXutHOFSYJe25zc1vGxsv; c_user=100016865703374; xs=29%3AOl2lBtCHN_uMRA%3A2%3A1591565467%3A17482%3A10881; m_pixel_ratio=1; wd=1600x761; act=1591652334218%2F13; presence=EDvF3EtimeF1591652339EuserFA21B16865703374A2EstateFDsb2F1591615022594EatF1591616159687Et3F_5b_5dEutc3F1591616159692G591652339527CEchF_7bCC; fr=0SE9GIw95RNs7jw6d.AWXyLGo0futKOQV9ER-gc9HYw_g.BeQdYM.OF.F7e.0.0.Be3sCS.AWUEdgrO; spin=r.1002218250_b.trunk_t.1591656797_s.1_v.2_"
}
*/