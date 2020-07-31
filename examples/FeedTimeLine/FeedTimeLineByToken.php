<?php  
require "../../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookFeedTimeLine;

$access_token = 'token';

$Feed = new FacebookFeedTimeLine();
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

  $post = $Feed->Process($cursor);

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
array(5) {
  [0]=>
  array(2) {
    ["userid"]=>
    string(15) "100000302451867"
    ["postid"]=>
    string(16) "3195385663814817"
  }
  [1]=>
  array(2) {
    ["userid"]=>
    string(15) "100019196373329"
    ["postid"]=>
    string(15) "589512871698587"
  }
  [2]=>
  array(2) {
    ["userid"]=>
    string(15) "100018001870770"
    ["postid"]=>
    string(15) "614990469111010"
  }
  [3]=>
  array(2) {
    ["userid"]=>
    string(15) "104309806571732"
    ["postid"]=>
    string(16) "1238076569861711"
  }
  [4]=>
  array(2) {
    ["userid"]=>
    string(15) "100038949079067"
    ["postid"]=>
    string(15) "247579619883672"
  }
}
*/