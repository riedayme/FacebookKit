<?php  
require "../../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookPostReactionUserList;

$cookie = 'cookie';

$postid = '1335773290088121';

$read = new FacebookPostReactionUserList();
$read->Required([
  'cookie' => $cookie,
  'useragent' => false, //  false for auto genereate
  'proxy' => false // false for not use proxy 
]);

$deep = false;
$count = 0;
$limit = 12;
$all_data = array();
do {

  $post = $read->Process($postid,$deep);

  if (!$post['status']) {
    echo $post['response'];
    break;
  }

  $data = $read->Extract($post);

  $all_data = array_merge($all_data,$data);

  if ($post['deep'] !== null) {
    $deep = $post['deep'];
  }else{
    $deep = false;
  }

  $count = $count+1;
} while ($deep !== false AND $count < $limit);

echo "<pre>";
var_dump($all_data);
echo "</pre>";

/*
array(9) {
  [0]=>
  array(4) {
    ["userlink"]=>
    string(31) "/profile.php?id=100054036154686"
    ["username"]=>
    string(10) "Huy Nguyen"
    ["photo"]=>
    string(267) "https://scontent-sin6-1.xx.fbcdn.net/v/t1.0-1/cp0/e15/q65/p32x32/114608480_101936588284213_2266949081671456165_o.jpg?_nc_cat=106&_nc_sid=dbb9e7&efg=eyJpIjoiYiJ9&_nc_ohc=FmRIJvSEVkYAX8hH1Be&_nc_ht=scontent-sin6-1.xx&tp=3&oh=088f8ce009e2f675083d505b5c717bcb&oe=5F478C86"
    ["sendrequest"]=>
    string(251) "https://mbasic.facebook.com/a/mobile/friends/add_friend.php?id=100054036154686&hf=profile_browser&suri=https%3A%2F%2Fmbasic.facebook.com%2Fufi%2Freaction%2Fprofile%2Fbrowser%2F%3Fft_ent_identifier%3D1335773290088121&fref=pb_likes&gfid=AQAU9-2RDbDGHXjI"
  }
  [1]=>
  array(4) {
    ["userlink"]=>
    string(31) "/profile.php?id=100048428143264"
    ["username"]=>
    string(10) "Trung Phan"
    ["photo"]=>
    string(267) "https://scontent-sin6-2.xx.fbcdn.net/v/t1.0-1/cp0/e15/q65/p32x32/116389790_158329929124611_4962541982343123201_n.jpg?_nc_cat=109&_nc_sid=dbb9e7&efg=eyJpIjoiYiJ9&_nc_ohc=GeVqAGakwT0AX-SavHn&_nc_ht=scontent-sin6-2.xx&tp=3&oh=1f6f17e8cd1a790bc2b256aac8dd9e36&oe=5F4AB635"
    ["sendrequest"]=>
    string(251) "https://mbasic.facebook.com/a/mobile/friends/add_friend.php?id=100048428143264&hf=profile_browser&suri=https%3A%2F%2Fmbasic.facebook.com%2Fufi%2Freaction%2Fprofile%2Fbrowser%2F%3Fft_ent_identifier%3D1335773290088121&fref=pb_likes&gfid=AQAgec_uWMo_DI0n"
  }
  [2]=>
  array(4) {
    ["userlink"]=>
    string(17) "/LuyenHaiDang.Net"
    ["username"]=>
    string(20) "Luyện Hải Đăng"
    ["photo"]=>
    string(284) "https://scontent-sin6-1.xx.fbcdn.net/v/t1.30497-1/cp0/e15/q65/c9.0.32.32a/p32x32/84241059_189132118950875_4138507100605120512_n.jpg?_nc_cat=1&_nc_sid=dbb9e7&efg=eyJpIjoiYiJ9&_nc_ohc=ZOOKeoZysIMAX8J7hrc&_nc_ht=scontent-sin6-1.xx&_nc_tp=5&oh=cfdde6374e473aff1ab537a63b2d741d&oe=5F487B18"
    ["sendrequest"]=>
    string(251) "https://mbasic.facebook.com/a/mobile/friends/add_friend.php?id=100043993322108&hf=profile_browser&suri=https%3A%2F%2Fmbasic.facebook.com%2Fufi%2Freaction%2Fprofile%2Fbrowser%2F%3Fft_ent_identifier%3D1335773290088121&fref=pb_likes&gfid=AQAEm_RA-7M5zmQA"
  }
  [3]=>
  array(4) {
    ["userlink"]=>
    string(7) "/ptt270"
    ["username"]=>
    string(20) "Phạm Tiến Thành"
    ["photo"]=>
    string(266) "https://scontent-sin6-1.xx.fbcdn.net/v/t1.0-1/cp0/e15/q65/p32x32/110917008_280221400067150_265031592992397606_o.jpg?_nc_cat=107&_nc_sid=dbb9e7&efg=eyJpIjoiYiJ9&_nc_ohc=l-euWkXcMxIAX9s4oVc&_nc_ht=scontent-sin6-1.xx&tp=3&oh=47c4c067a5652de55bb1b702213d60bd&oe=5F48B7E0"
    ["sendrequest"]=>
    string(251) "https://mbasic.facebook.com/a/mobile/friends/add_friend.php?id=100042378771464&hf=profile_browser&suri=https%3A%2F%2Fmbasic.facebook.com%2Fufi%2Freaction%2Fprofile%2Fbrowser%2F%3Fft_ent_identifier%3D1335773290088121&fref=pb_likes&gfid=AQB2wZbKxyLyRFwj"
  }
  [4]=>
  array(4) {
    ["userlink"]=>
    string(31) "/profile.php?id=100040359199443"
    ["username"]=>
    string(12) "Phú Thịnh"
    ["photo"]=>
    string(341) "https://scontent-sin6-2.xx.fbcdn.net/v/t1.0-1/cp0/e15/q65/p32x32/109962210_298669871488317_202852694047551678_n.jpg?_nc_cat=109&_nc_sid=dbb9e7&efg=eyJpIjoiYiJ9&_nc_ohc=o5dTgFbqALIAX8j5DUd&_nc_oc=AQlUUUdTz6U6Klk_jwm368isQI2oD1smokLEkwM-Vof8dhAbrKwmm7L-SjplINh46Qk&_nc_ht=scontent-sin6-2.xx&tp=3&oh=421bba5c0b95bf63e6290a21b056f029&oe=5F499C41"
    ["sendrequest"]=>
    string(251) "https://mbasic.facebook.com/a/mobile/friends/add_friend.php?id=100040359199443&hf=profile_browser&suri=https%3A%2F%2Fmbasic.facebook.com%2Fufi%2Freaction%2Fprofile%2Fbrowser%2F%3Fft_ent_identifier%3D1335773290088121&fref=pb_likes&gfid=AQDwZNy48Zrrlh9H"
  }
  [5]=>
  array(4) {
    ["userlink"]=>
    string(22) "/long.nguyenngohoang.5"
    ["username"]=>
    string(25) "Nguyễn Ngô Hoàng Long"
    ["photo"]=>
    string(271) "https://scontent-sin6-2.xx.fbcdn.net/v/t1.0-1/cp0/e15/q65/p32x32/103380919_276834237003680_1045046707207328146_n.jpg?_nc_cat=109&_nc_sid=dbb9e7&efg=eyJpIjoiYiJ9&_nc_ohc=iRTlAcnV9CAAX-sOTFe&_nc_ht=scontent-sin6-2.xx&_nc_tp=3&oh=e669b2fde1a922a42a53b6360cc6fc0b&oe=5F4A2D35"
    ["sendrequest"]=>
    string(251) "https://mbasic.facebook.com/a/mobile/friends/add_friend.php?id=100040313175899&hf=profile_browser&suri=https%3A%2F%2Fmbasic.facebook.com%2Fufi%2Freaction%2Fprofile%2Fbrowser%2F%3Fft_ent_identifier%3D1335773290088121&fref=pb_likes&gfid=AQA-wf5TAAliOzDT"
  }
  [6]=>
  array(4) {
    ["userlink"]=>
    string(9) "/duy.lewd"
    ["username"]=>
    string(3) "Duy"
    ["photo"]=>
    string(270) "https://scontent-sin6-2.xx.fbcdn.net/v/t1.0-1/cp0/e15/q65/p32x32/106637925_273213967347837_471244078433192085_o.jpg?_nc_cat=102&_nc_sid=dbb9e7&efg=eyJpIjoiYiJ9&_nc_ohc=T5yFedG7ElIAX-43oBa&_nc_ht=scontent-sin6-2.xx&_nc_tp=3&oh=2feeeb6776614e444e4e7dfdd72383f1&oe=5F4B017A"
    ["sendrequest"]=>
    string(251) "https://mbasic.facebook.com/a/mobile/friends/add_friend.php?id=100039777061880&hf=profile_browser&suri=https%3A%2F%2Fmbasic.facebook.com%2Fufi%2Freaction%2Fprofile%2Fbrowser%2F%3Fft_ent_identifier%3D1335773290088121&fref=pb_likes&gfid=AQAaqkh029BkVUGh"
  }
  [7]=>
  array(4) {
    ["userlink"]=>
    string(7) "/vddtan"
    ["username"]=>
    string(12) "Duy Tân Võ"
    ["photo"]=>
    string(271) "https://scontent-sin6-1.xx.fbcdn.net/v/t1.0-1/cp0/e15/q65/p32x32/106337694_281307579868181_3486568470663587760_n.jpg?_nc_cat=111&_nc_sid=dbb9e7&efg=eyJpIjoiYiJ9&_nc_ohc=zukLa3SqWQ0AX8Sei_r&_nc_ht=scontent-sin6-1.xx&_nc_tp=3&oh=64d1cf97901db5bc8cb0ca2f6d71b6ab&oe=5F4924A6"
    ["sendrequest"]=>
    string(251) "https://mbasic.facebook.com/a/mobile/friends/add_friend.php?id=100039668237950&hf=profile_browser&suri=https%3A%2F%2Fmbasic.facebook.com%2Fufi%2Freaction%2Fprofile%2Fbrowser%2F%3Fft_ent_identifier%3D1335773290088121&fref=pb_likes&gfid=AQBFpVKgGZSJAKJr"
  }
  [8]=>
  array(4) {
    ["userlink"]=>
    string(13) "/Audreyytrann"
    ["username"]=>
    string(13) "Trần Hoàng"
    ["photo"]=>
    string(266) "https://scontent-sin6-1.xx.fbcdn.net/v/t1.0-1/cp0/e15/q65/p32x32/96145805_235269597801813_4817275245209583616_n.jpg?_nc_cat=111&_nc_sid=dbb9e7&efg=eyJpIjoiYiJ9&_nc_ohc=7NqIETMfajYAX_OcYg6&_nc_ht=scontent-sin6-1.xx&tp=3&oh=97845b27b1c6fdd62efc969ea1addcab&oe=5F49C561"
    ["sendrequest"]=>
    string(251) "https://mbasic.facebook.com/a/mobile/friends/add_friend.php?id=100039563239316&hf=profile_browser&suri=https%3A%2F%2Fmbasic.facebook.com%2Fufi%2Freaction%2Fprofile%2Fbrowser%2F%3Fft_ent_identifier%3D1335773290088121&fref=pb_likes&gfid=AQDKWxOS94ObVlWr"
  }
}
*/