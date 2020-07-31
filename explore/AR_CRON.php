<?php 
/**
* Facebook Auto Reaction v1.3 [CRON Version]
* Last Update 13 Juni 2020
* Author : Faanteyki
*/
header('Content-type: text/plain');
ini_set('max_execution_time', 0);
ini_set('memory_limit', '-1');
set_time_limit(0);

require "../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookAuth;
use Riedayme\FacebookKit\FacebookCookie;
use Riedayme\FacebookKit\FacebookChecker;

use Riedayme\FacebookKit\FacebookGroupList;
use Riedayme\FacebookKit\FacebookFeedGroup;

use Riedayme\FacebookKit\FacebookFeedTimeLine;

use Riedayme\FacebookKit\FacebookPostComments;
use Riedayme\FacebookKit\FacebookPostReaction;


# Config
# ------
if (empty($_POST)) {
	$_POST['ChoiceTargetReaction'] = 'Timeline'; /* Group or Timeline */
	$_POST['GroupID'] = false; /* if target reaction is group this value must set */
	$_POST['Limit'] = '5';
	$_POST['React'] = 'WOW'; /* 'LIKE', 'LOVE', 'CARE', 'HAHA', 'WOW', 'SAD', 'ANGRY', 'RANDOM' */
	$_POST['Cookie'] = '_fbp=fb.1.1591218870915.1067997829;act=1591781179357%2F27;c_user=100016865703374;datr=Q5FYXhRV37LHftrlureny0a1;fr=1f0FKotkon9e6deNQ.AWV_wRCx8wHIFnF2k4TqJuA0Cj4.BeWJFD.Xk.F7g.0.0.Be4KLA.AWV8pBKj;presence=EDvF3EtimeF1591776407EuserFA21B16865703374A2EstateFDutF1591776407806CEchF_7bCC;sb=Q5FYXuL7Zf-hHWoDcYY-UiBh;spin=r.1002227224_b.trunk_t.1591776305_s.1_v.2_;wd=1440x757;xs=31%3AOt4FcPq7AR7waQ%3A2%3A1586409168%3A17482%3A10881;'; 
	$_POST['ReactComment'] = false; /* true or false */
}

Class FacebookAutoReaction
{

	public $login_data;
	public $cookie;
	public $access_token;	
	public $username;

	public $targetreaction;
	public $targetreactionname;
	public $limit;
	public $react;

	public $groupid;

	public function Auth($data,$ReAuth = false) 
	{

		/* check previous data if exist using the saved data */
		$results = self::ReadPreviousCookie(FacebookCookie::GetUIDCookie($data['cookie']));

		$login_with_cokie = false;
		if ($results) {

			echo "[INFO] Check Live Cookie".PHP_EOL;

			$check_cookie = FacebookChecker::CheckLiveCookie($results['cookie']);
			if (!$check_cookie) die("[ERROR] cookie tidak bisa digunakan".PHP_EOL);

			$login_with_cokie = true;

			if ($ReAuth) {

				echo "[INFO] Reauth Cookie".PHP_EOL;

				$auth = new FacebookAuth();
				$results =$auth->AuthUsingCookie($results['cookie']);

				echo "[INFO] Menyimpan Data Login".PHP_EOL;

				self::SaveCookie($results);
			}
		}

		if (!$results OR !$login_with_cokie) {

			echo "[INFO] Validate Cookie".PHP_EOL;

			$auth = new FacebookAuth();
			$results =$auth->AuthUsingCookie($data['cookie']);

			echo "[INFO] Menyimpan Data Login".PHP_EOL;

			self::SaveCookie($results);
		}

		$this->login_data = $data;
		$this->username = $results['username'];
		$this->cookie = $results['cookie'];
		$this->access_token = $results['access_token'];
		if (!$ReAuth) {
			$this->targetreaction = $_POST["ChoiceTargetReaction"];
			if ($this->targetreaction == 'Group') {
				$this->targetreactionname = 'Group';
				$this->groupid = $_POST['GroupID'];
			}else{
				$this->targetreactionname = 'Timeline';
			}
			$this->limit = $_POST["Limit"];
			$this->react = $_POST["React"];
		}
	}

	public function SaveCookie($data){

		$filename = "log/".$data['userid'].".json";

		return file_put_contents($filename, json_encode($data,JSON_PRETTY_PRINT));
	}

	public function ReadPreviousCookie($data)
	{

		$filename = "log/{$data}.json";
		if (file_exists($filename)) 
		{
			$read = file_get_contents($filename);
			$read = json_decode($read,TRUE);
			return $read;
		}else{
			return false;
		}
	}	

	public function GetFeed()
	{

		echo "[INFO] Membaca Feed {$this->targetreactionname}".PHP_EOL;

		if ($this->targetreaction == 'Group') 
		{
			$Feed = new FacebookFeedGroup();
			$Feed->SetAccessToken($this->access_token);

			$results =$Feed->GetFeedGroupByToken([
				'groupid' => $this->groupid,
				'limit' => $this->limit
			]);
		}
		elseif ($this->targetreaction == 'Timeline') 
		{
			$Feed = new FacebookFeedTimeLine();
			$Feed->SetAccessToken($this->access_token);		
			$results =$Feed->GetTimeLineByToken($this->limit);
		}

		/* check if feed not loaded */
		if (!$results) 
		{
			echo "[ERROR] Mendapatkan Feed".PHP_EOL;

			self::ReAuth();
			return false;
		}

		echo "[INFO] Sukses Mendapatkan Feed".PHP_EOL;

		return $results;
	}

	public function GetComment($postid)
	{
		$data = [
			'postid' => $postid,
			'limit' => 10,
		];
		$GetComment = new FacebookPostComments();
		$GetComment->SetCookie($this->cookie);
		$GetComment->GetComment($data);
		$results = $GetComment->ResultsIDS();

		if (!$results) 
		{
			return false;
		}

		return $results;
	}

	public function ReactPost($datapost,$is_comment = false)
	{

		$posttype = ($is_comment == true) ? "Komentar" : "Post";

		$datapost['url'] = "https://www.facebook.com/{$datapost['postid']}";

		/* sync react post with log file */
		$sync = self::SyncReact($datapost['postid']);
		if ($sync) 
		{
			echo "[SKIP] React {$posttype} {$datapost['url']} Sudah Diproses.".PHP_EOL;
			return false;
		}

		$type = ($this->react == 'RANDOM') ? $_POST["React"] : $this->react;

		echo "[INFO] Proses React {$type} {$datapost['postid']}".PHP_EOL;

		$react = new FacebookPostReaction();
		$react->SetCookie($this->cookie);
		$process = $react->ReactPostByScraping([
			'postid' => $datapost['postid'], 
			'type' => $type
		]);

		if ($process != false) 
		{

			if ($process == 'URL_NOTFOUND') 
			{
				echo "[FAILED] URL React pada {$posttype} {$datapost['url']} tidak ditemukan.".PHP_EOL;	
			}elseif ($process == 'UNREACT') 
			{
				echo "[SKIP] React {$posttype} {$datapost['url']} Sudah Diproses.".PHP_EOL;	
				self::SaveLog($datapost['postid']);	
			}else{
				echo "[SUCCESS] React {$posttype} {$datapost['url']}".PHP_EOL;
				self::SaveLog($datapost['postid']);
				return true;
			}
		}else{
			echo "[FAILED] React {$posttype} {$datapost['url']}, Kesalahan pada kode.".PHP_EOL;
		}

		return false;
	}

	public function ReAuth()
	{

		echo "[INFO] Mencoba login kembali".PHP_EOL;
		self::Auth($this->login_data,true);
	}

	public function SyncReact($postid)
	{

		$ReadLog = self::ReadLog();

		if (is_array($ReadLog) AND in_array($postid, $ReadLog)) 
		{
			return true;
		}

		return false;
	}

	public function ReadLog()
	{		

		$logfilename = "log/{$this->targetreactionname}{$this->groupid}-{$this->username}";
		$log_url = array();
		if (file_exists($logfilename)) 
		{
			$log_url = file_get_contents($logfilename);
			$log_url  = explode(PHP_EOL, $log_url);
		}

		return $log_url;
	}

	public function SaveLog($datapost)
	{
		return file_put_contents("log/{$this->targetreactionname}{$this->groupid}-{$this->username}", $datapost.PHP_EOL, FILE_APPEND);
	}
}

Class Worker
{
	public static function Run()
	{

		echo " --- Facebook Auto Reaction v1.3 ---".PHP_EOL;

		$data['cookie'] = $_POST["Cookie"];

		$delay_default = 10;
		$delay = 10;
		/* Call Class */
		$Working = new FacebookAutoReaction();
		$Working->Auth($data);

		$react_comment = $_POST["ReactComment"];
		$ReactPost = 0;

		$FeedList = $Working->GetFeed();

		if (empty($FeedList)) 
		{
			echo "[INFO] Tidak ditemukan Post, Coba lagi setelah {$delayfeed} detik".PHP_EOL;
		}

		$temp_post_process = 0;
		foreach ($FeedList as $key => $post) 
		{

			/* when ReactPost 5 reset sleep value to default */
			if ($ReactPost >= 5) 
			{
				$delay = $delay_default;
				$ReactPost = 0;
			}	

			$process_post = $Working->ReactPost($post);

			if ($process_post) 
			{
				echo "[INFO] Delay {$delay}".PHP_EOL;
				sleep($delay);
				$delay = $delay+5;
				$ReactPost++;
				$temp_post_process++;
			}

			if ($react_comment == true) 
			{
				echo "[INFO] Membaca Komentar Post {$post['postid']}".PHP_EOL;

				$comment = $Working->GetComment($post['postid']);
				if (!$comment) 
				{
					echo "[INFO] Tidak ada komentar pada post {$post['postid']}".PHP_EOL;
					continue;
				}

				foreach ($comment as $commentid) 
				{

					/* when ReactPost 5 reset sleep value to default */
					if ($ReactPost >= 5) 
					{
						$delay = $delay_default;
						$ReactPost = 0;
					}	

					$commentpost['postid'] = $commentid;
					$process_comment = $Working->ReactPost($commentpost,true);

					if ($process_comment) 
					{
						echo "[INFO] Delay {$delay}".PHP_EOL;
						sleep($delay);

						$delay = $delay+5;
						$ReactPost++;
					}

				}
			}

		}		

	}
}

$filehandle = fopen("log/".FacebookCookie::GetUIDCookie($_POST['Cookie']).".txt", "c+");
if (flock($filehandle, LOCK_EX | LOCK_NB)) {

	Worker::Run();
	
	flock($filehandle, LOCK_UN);  
}	
// use at you own risk