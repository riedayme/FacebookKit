<?php  
require "../../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookPostCommentsRead;
use Riedayme\FacebookKit\FacebookPostCommentsReplyRead;

$cookie = 'cookie';

$postid = '3079795912133643';

$read = new FacebookPostCommentsRead();
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


/* read reply comment */
$readreply = new FacebookPostCommentsReplyRead();
$readreply->Required([
	'cookie' => $cookie,
	'useragent' => false, //  false for auto genereate
	'proxy' => false // false for not use proxy 
]);

$comment_data = array();
foreach ($all_data as $comment) {

	if ($comment['reply_url']) {

		$deep = false;
		$count = 0;
		$limit = 1;
		do {

			$post = $readreply->Process($comment['reply_url'],$deep);

			if (!$post['status']) {
				echo $post['response'];
				break;
			}

			$data = $readreply->Extract($post);

			$comment_data[] = [
				'userid' => $comment['userid'],					
				'username' => $comment['username'],
				'commentid' => $comment['commentid'],
				'reply' => $data
			];

			if ($post['deep'] !== null) {
				$deep = $post['deep'];
			}else{
				$deep = false;
			}

			$count = $count+1;
		} while ($deep !== false AND $count < $limit);

	}else{
		$comment_data[] = [
			'userid' => $comment['userid'],					
			'username' => $comment['username'],
			'commentid' => $comment['commentid'],
			'reply' => false
		];
	}

}

echo "<pre>";
var_dump($comment_data);
echo "</pre>";

/*
array(5) {
  [0]=>
  array(4) {
    ["userid"]=>
    string(27) "/kintal15?refid=52&__tn__=R"
    ["username"]=>
    string(4) "Alfi"
    ["commentid"]=>
    string(33) "3079795912133643_3079806975465870"
    ["reply"]=>
    array(3) {
      [0]=>
      array(3) {
        ["userid"]=>
        string(19) "/mrezkys12?__tn__=R"
        ["username"]=>
        string(13) "Mrezkys Rezky"
        ["commentid"]=>
        string(16) "3079807525465815"
      }
      [1]=>
      array(3) {
        ["userid"]=>
        string(19) "/mrezkys12?__tn__=R"
        ["username"]=>
        string(13) "Mrezkys Rezky"
        ["commentid"]=>
        string(16) "3079807762132458"
      }
      [2]=>
      array(3) {
        ["userid"]=>
        string(18) "/kintal15?__tn__=R"
        ["username"]=>
        string(4) "Alfi"
        ["commentid"]=>
        string(16) "3079808218799079"
      }
    }
  }
  [1]=>
  array(4) {
    ["userid"]=>
    string(44) "/kickernewbie.kickernewbie?refid=52&__tn__=R"
    ["username"]=>
    string(4) "Satt"
    ["commentid"]=>
    string(33) "3079795912133643_3079828875463680"
    ["reply"]=>
    bool(false)
  }
  [2]=>
  array(4) {
    ["userid"]=>
    string(33) "/ahmadsaugi.gis?refid=52&__tn__=R"
    ["username"]=>
    string(11) "Ahmad Saugi"
    ["commentid"]=>
    string(33) "3079795912133643_3079920142121220"
    ["reply"]=>
    array(1) {
      [0]=>
      array(3) {
        ["userid"]=>
        string(35) "/kickernewbie.kickernewbie?__tn__=R"
        ["username"]=>
        string(4) "Satt"
        ["commentid"]=>
        string(16) "3079948468785054"
      }
    }
  }
  [3]=>
  array(4) {
    ["userid"]=>
    string(31) "/adrian.ryuga?refid=52&__tn__=R"
    ["username"]=>
    string(6) "Adrian"
    ["commentid"]=>
    string(33) "3079795912133643_3079955502117684"
    ["reply"]=>
    bool(false)
  }
  [4]=>
  array(4) {
    ["userid"]=>
    string(37) "/wahyu.amirulloh.35?refid=52&__tn__=R"
    ["username"]=>
    string(15) "Wahyu Amirulloh"
    ["commentid"]=>
    string(33) "3079795912133643_3079990188780882"
    ["reply"]=>
    bool(false)
  }
}
*/