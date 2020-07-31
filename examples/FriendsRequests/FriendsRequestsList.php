<?php  
require "../../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookFriendsRequestsList;

$cookie = 'cookie';

$read = new FacebookFriendsRequestsList();
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
  array(5) {
    ["userid"]=>
    string(15) "100012974108550"
    ["username"]=>
    string(12) "Rizky Dharma"
    ["photo"]=>
    string(60) "https://static.xx.fbcdn.net/rsrc.php/v3/ym/r/Gjhrhb7r0lb.png"
    ["linkconfirm"]=>
    string(276) "/a/notifications.php?confirm=100012974108550&redir=https%3A%2F%2Fmbasic.facebook.com%2Ffriends%2Fcenter%2Frequests%3Fmfl_act%3D1%23last_acted&seenrequesttime=1592391461&refparam=m_find_friends&ufli=1&floc=friend_center_requests&view_as_id=100016865703374&gfid=AQApAHuExb_TDEHV"
    ["linkreject"]=>
    string(275) "/a/notifications.php?delete=100012974108550&redir=https%3A%2F%2Fmbasic.facebook.com%2Ffriends%2Fcenter%2Frequests%3Fmfl_act%3D1%23last_acted&seenrequesttime=1592391461&refparam=m_find_friends&ufli=1&floc=friend_center_requests&view_as_id=100016865703374&gfid=AQCUY1WSR-rn8zvg"
  }
}
*/