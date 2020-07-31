<?php  
require "../../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookFriendsSuggest;

$cookie = 'cookie';

$read = new FacebookFriendsSuggest();
$read->Required([
  'cookie' => $cookie,
  'useragent' => false, //  false for auto genereate
  'proxy' => false // false for not use proxy 
]);

$deep = false;
$count = 0;
$limit = 1;
$all_data = array();
do {

  $post = $read->Process($deep);

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
array(1) {
  [0]=>
  array(4) {
    ["userid"]=>
    string(15) "100035808846258"
    ["username"]=>
    string(14) "Yudistira Arya"
    ["photo"]=>
    string(319) "https://scontent-sin6-1.xx.fbcdn.net/v/t1.0-1/cp0/e15/q65/p100x100/96390021_262605901609703_3369416553974988800_o.jpg?_nc_cat=101&_nc_sid=dbb9e7&efg=eyJpIjoiYiJ9&_nc_oc=AQkpihLQmfTMc5bk0bMfPacdylYXXaApmbmMyKuRrQII6dNQ8LQ8v-fsapGx42CWLDY&_nc_ht=scontent-sin6-1.xx&_nc_tp=3&oh=ba0627d2af95f758e00971e42bd7146d&oe=5F1006C0"
    ["linksendrequest"]=>
    string(310) "/a/mobile/friends/add_friend.php?id=100035808846258&hf=friend_browser&pl=%2Ffind-friends%2Findex.php&sc=0.0633591&so=pysu&signature=350208451&suri=https%3A%2F%2Fmbasic.facebook.com%2Ffriends%2Fcenter%2Fsuggestions%2F%3Fmfl_act%3D1%23last_acted&ufli=1&mfle=1&floc=pymk&frefs=friends_center&gfid=AQD2GD51po4x7jXh"
  }
}
*/