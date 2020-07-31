<?php  
require "../../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookFeedGroup;

$access_token = 'token';

$groupid = '697332711026460'; // j2team girl

$Feed = new FacebookFeedGroup();
$Feed->Required([
  'access_token' => $access_token,
  'useragent' => false, //  false for auto genereate
  'proxy' => false // false for not use proxy 
]);

$cursor = false;
$count = 0;
$limit = 1;
$all_data = array();
do {

  $post = $Feed->Process($groupid,$cursor);

  if (!$post['status']) {
    echo $post['response'];
    break;
  }

  $data = $Feed->Extract($post);

  $all_data = array_merge($all_data,$data);

  if ($post['cursor'] !== null) {
    $cursor = $post['cursor'];
  }else{
    $cursor = false;
  }

  $count = $count+1;
} while ($cursor !== false AND $count < $limit);

echo "<pre>";
var_dump($all_data);
echo "</pre>";

/*
array(10) {
  [0]=>
  array(2) {
    ["userid"]=>
    string(15) "697332711026460"
    ["postid"]=>
    string(15) "771621283597602"
  }
  [1]=>
  array(2) {
    ["userid"]=>
    string(15) "697332711026460"
    ["postid"]=>
    string(15) "768675733892157"
  }
  [2]=>
  array(2) {
    ["userid"]=>
    string(15) "697332711026460"
    ["postid"]=>
    string(15) "771562783603452"
  }
  [3]=>
  array(2) {
    ["userid"]=>
    string(15) "697332711026460"
    ["postid"]=>
    string(15) "756212738471790"
  }
  [4]=>
  array(2) {
    ["userid"]=>
    string(15) "697332711026460"
    ["postid"]=>
    string(15) "771088230317574"
  }
  [5]=>
  array(2) {
    ["userid"]=>
    string(15) "697332711026460"
    ["postid"]=>
    string(15) "771065153653215"
  }
  [6]=>
  array(2) {
    ["userid"]=>
    string(15) "697332711026460"
    ["postid"]=>
    string(15) "754955181930879"
  }
  [7]=>
  array(2) {
    ["userid"]=>
    string(15) "697332711026460"
    ["postid"]=>
    string(15) "771275476965516"
  }
  [8]=>
  array(2) {
    ["userid"]=>
    string(15) "697332711026460"
    ["postid"]=>
    string(15) "770974873662243"
  }
  [9]=>
  array(2) {
    ["userid"]=>
    string(15) "697332711026460"
    ["postid"]=>
    string(15) "771069883652742"
  }
}
*/