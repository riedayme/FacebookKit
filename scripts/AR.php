<?php  
/**
* Facebook Auto Reaction v1.3
* Last Update 13 Juni 2020
* Author : Faanteyki
*/
require "../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookAuth;
use Riedayme\FacebookKit\FacebookCookie;
use Riedayme\FacebookKit\FacebookChecker;

use Riedayme\FacebookKit\FacebookGroupList;
use Riedayme\FacebookKit\FacebookFeedGroup;

use Riedayme\FacebookKit\FacebookFeedTimeLine;

use Riedayme\FacebookKit\FacebookPostComments;
use Riedayme\FacebookKit\FacebookPostReaction;

Class InputHelper
{

	public function GetInputChoiceLogin($data = false) 
	{

		if ($data) return $data;

		echo "Pilihan Login Menggunakan : ".PHP_EOL;
		echo "[c] Masuk menggunakan cookie".PHP_EOL;
		echo "[p] Masuk menggunakan username dan password".PHP_EOL;

		$input = trim(fgets(STDIN));

		if (!in_array(strtolower($input),['c','p'])) 
		{
			die("Pilihan tidak diketahui".PHP_EOL);
		}

		return (!$input) ? die('Pilihan masih Kosong'.PHP_EOL) : $input;
	}	

	public function GetInputCookie($data = false) {

		if ($data) return $data;

		$CheckPreviousCookie = FacebookAutoReaction::CheckPreviousCookie();

		if ($CheckPreviousCookie) 
		{
			echo "Anda Memiliki Cookie yang tersimpan pilih angkanya dan gunakan kembali : ".PHP_EOL;
			foreach ($CheckPreviousCookie as $key => $cookie) 
			{
				echo "[{$key}]".$cookie['username'].PHP_EOL;
			}
			echo "[x] Masuk menggunakan cookie baru".PHP_EOL;

			echo "Pilihan Anda : ".PHP_EOL;

			$input = strtolower(trim(fgets(STDIN)));			

			if ($input != 'x') 
			{

				if (strval($input) !== strval(intval($input))) 
				{
					die("Salah memasukan format, pastikan hanya angka".PHP_EOL);
				}

				return $input;
			}
		}		

		echo "Masukan Cookie : ".PHP_EOL;

		$input = trim(fgets(STDIN));

		return (!$input) ? die('Cookie Masih Kosong'.PHP_EOL) : $input;
	}		

	public function GetInputUsername($data = false) 
	{

		if ($data) return $data;

		echo "Masukan Username : ".PHP_EOL;

		$input = trim(fgets(STDIN));

		return (!$input) ? die('Username Masih Kosong'.PHP_EOL) : $input;
	}

	public function GetInputPassword($data = false) 
	{

		if ($data) return $data;

		echo "Masukan Password: ".PHP_EOL;

		return trim(fgets(STDIN));
	}	

	public function GetInputLimit($data = false) 
	{

		if ($data) return $data;

		echo "Masukan Limit Feed (angka): ".PHP_EOL;

		$input = trim(fgets(STDIN));

		if (strval($input) !== strval(intval($input))) 
		{
			die("Salah memasukan format, pastikan hanya angka".PHP_EOL);
		}

		return (!$input) ? die('Limit Feed Masih Kosong'.PHP_EOL) : $input;
	}

	public function GetInputReact($data = false) 
	{

		if (strtoupper($data) == 'RANDOM') 
		{
			$reactlist = ['LIKE', 'LOVE', 'CARE', 'WOW'];
			return $reactlist[array_rand($reactlist)];
		}

		if ($data) return $data;

		echo "Masukan Reaksi yang dikirim : [LIKE, LOVE, CARE, HAHA, WOW, SAD, ANGRY, RANDOM]".PHP_EOL;

		$input = strtoupper(trim(fgets(STDIN)));

		$react = ['LIKE', 'LOVE', 'CARE', 'HAHA', 'WOW', 'SAD', 'ANGRY', 'RANDOM'];

		if (!in_array($input,$react)) 
		{
			die("Reaksi Pilihan tidak valid".PHP_EOL);
		}


		return (!$input) ? die('Reaction Masih Kosong'.PHP_EOL) : $input;
	}

	public function GetInputReactComment($data = false) 
	{

		if ($data) return $data;

		echo "React Comment Juga ? (y/n): ".PHP_EOL;

		$input = trim(fgets(STDIN));

		if (!in_array(strtolower($input),['y','n'])) 
		{
			die("Pilihan tidak diketahui".PHP_EOL);
		}

		return (!$input) ? die('Pilihan masih Kosong'.PHP_EOL) : $input;
	}

	public function GetInputChoiceTargetReaction($data = false) 
	{

		if ($data) return $data;

		echo "Pilihan Jenis Target Feed : ".PHP_EOL;
		echo "[1] Feed Timeline".PHP_EOL;
		echo "[2] Feed Group".PHP_EOL;

		$input = trim(fgets(STDIN));

		if (!in_array(strtolower($input),['1','2'])) 
		{
			die("Pilihan tidak diketahui".PHP_EOL);
		}

		return (!$input) ? die('Pilihan masih Kosong'.PHP_EOL) : $input;
	}		

	public function GetInputGroupName($data = false) {

		if ($data) return $data;

		echo "Cari Nama Group (karakter): ".PHP_EOL;

		$input = trim(fgets(STDIN));

		return (!$input) ? die('Nama Group Masih Kosong'.PHP_EOL) : $input;
	}	

	public function GetInputChoiceGroup($data = false) {

		if ($data) return $data;

		echo "Masukan Group yang dipilih (angka): ".PHP_EOL;

		$input = trim(fgets(STDIN));

		if (strval($input) !== strval(intval($input))) {
			die("Salah memasukan format, pastikan hanya angka".PHP_EOL);
		}

		return $input;
	}				
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

		if ($data['type'] == 'previous_cookie') 
		{
			$results = self::ReadPreviousCookie($data['cookie']);

			echo "[INFO] Check Live Cookie".PHP_EOL;

			$check_cookie = FacebookChecker::CheckLiveCookie($results['cookie']);
			if (!$check_cookie) die("[ERROR] cookie tidak bisa digunakan".PHP_EOL);

			if ($ReAuth) {

				echo "[INFO] Reauth Cookie".PHP_EOL;

				$auth = new FacebookAuth();
				$results =$auth->AuthUsingCookie($results['cookie']);

				echo "[INFO] Menyimpan Data Login".PHP_EOL;

				self::SaveCookie($results);
			}
		}elseif ($data['type'] == 'new_cookie') {
			echo "[INFO] Validate Cookie".PHP_EOL;

			$auth = new FacebookAuth();
			$results =$auth->AuthUsingCookie($data['cookie']);

			echo "[INFO] Menyimpan Data Login".PHP_EOL;

			self::SaveCookie($results);
		}elseif ($data['type'] == 'login') {
			echo "[INFO] Masuk menggunakan username dan password".PHP_EOL;

			$auth = new FacebookAuth();
			$results =$auth->AuthLoginByMobile($data['username'],$data['password']);

			echo "[INFO] Menyimpan Data Login".PHP_EOL;

			self::SaveCookie($results);
		}

		$this->login_data = $data;
		$this->username = $results['username'];
		$this->cookie = $results['cookie'];
		$this->access_token = $results['access_token'];
		if (!$ReAuth) {
			$this->targetreaction = InputHelper::GetInputChoiceTargetReaction();
			if ($this->targetreaction == '2') {
				$this->targetreactionname = 'Group';
				$this->groupid = self::ChoiceGroup();
			}else{
				$this->targetreactionname = 'Timeline';
			}
			$this->limit = InputHelper::GetInputLimit();
			$this->react = InputHelper::GetInputReact();
		}
	}

	public function SaveCookie($data){

		$filename = 'log/log-cookie.json';

		if (file_exists($filename)) 
		{
			$read = file_get_contents($filename);
			$read = json_decode($read,true);
			$dataexist = false;
			foreach ($read as $key => $logdata) 
			{
				if ($logdata['userid'] == $data['userid']) 
				{
					$inputdata[] = $data;
					$dataexist = true;
				}else{
					$inputdata[] = $logdata;
				}
			}

			if (!$dataexist) 
			{
				$inputdata[] = $data;
			}
		}else{
			$inputdata[] = $data;
		}

		return file_put_contents($filename, json_encode($inputdata,JSON_PRETTY_PRINT));
	}

	public function CheckPreviousCookie()
	{

		$filename = 'log/log-cookie.json';
		if (file_exists($filename)) 
		{
			$read = file_get_contents($filename);
			$read = json_decode($read,TRUE);
			foreach ($read as $key => $logdata) 
			{
				$inputdata[] = $logdata;
			}

			return $inputdata;
		}else{
			return false;
		}
	}

	public function ReadPreviousCookie($data)
	{

		$filename = 'log/log-cookie.json';
		if (file_exists($filename)) 
		{
			$read = file_get_contents($filename);
			$read = json_decode($read,TRUE);
			foreach ($read as $key => $logdata) 
			{
				if ($key == $data) 
				{
					$inputdata = $logdata;
					break;
				}
			}

			return $inputdata;
		}else{
			die("file tidak ditemukan");
		}
	}	

	public function GetFeed()
	{

		echo "[INFO] Membaca Feed {$this->targetreactionname}".PHP_EOL;

		if ($this->targetreaction == '2') 
		{
			$Feed = new FacebookFeedGroup();
			$Feed->SetAccessToken($this->access_token);

			$results =$Feed->GetFeedGroupByToken([
				'groupid' => $this->groupid,
				'limit' => $this->limit
				]);
		}
		elseif ($this->targetreaction == '1') 
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

		$type = ($this->react == 'RANDOM') ? InputHelper::GetInputReact('RANDOM') : $this->react;

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

	public function ChoiceGroup()
	{
		echo "[INFO] Mendapatkan List Group".PHP_EOL;

		$Group = new FacebookGroupList();
		$Group->SetCookie($this->cookie);

		$results =$Group->GetGroupListByScraping();

		echo "[INFO] Ditemukan ".count($results)." Group".PHP_EOL;

		$search = InputHelper::GetInputGroupName();

		$search_results = array();
		foreach ($results as $key => $group) {
			if (preg_match("/{$search}/i", $group['name'])) {
				$search_results[] = "[{$key}]".$group['name'].PHP_EOL;
			}
		}

		if (!$search_results) {
			echo "[INFO] Group tidak ditemukan, coba kembali.".PHP_EOL;
			return self::ChoiceGroup();
		}else{
			echo "[INFO] Daftar Group yang ditemukan : ".PHP_EOL;			
			echo implode('', $search_results);
		}

		$choice = InputHelper::GetInputChoiceGroup();
		return $results[$choice]['id'];
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
	public function Run()
	{

		echo " --- Facebook Auto Reaction v1.3 ---".PHP_EOL;

		$login_choice = InputHelper::GetInputChoiceLogin();

		if ($login_choice == 'c') {
			$data['cookie'] = InputHelper::GetInputCookie();
			if (strval($data['cookie']) !== strval(intval($data['cookie']))) 
			{
				$data['type'] = 'new_cookie';
			}else{
				$data['type'] = 'previous_cookie';
			}
		}


		if ($login_choice == 'p') {
			$data['type'] = 'login';
			$data['username'] = InputHelper::GetInputUsername();

			if (!is_array($data['username'])) 
			{
				$data['password'] = InputHelper::GetInputPassword();
			}
		}

		$delay_default = 10;
		$delay = 10;
		$delayfeed_default = 10;
		$delayfeed = 10;

		/* Call Class */
		$Working = new FacebookAutoReaction();
		$Working->Auth($data);

		$react_comment = InputHelper::GetInputReactComment();

		$nofeed = 0;
		$ReactPost = 0;
		while (true) 
		{

			/* when nofeed 5 reset sleep value to default */
			if ($nofeed >= 5) 
			{
				$delayfeed = $delayfeed_default;
				$nofeed = 0;
			}

			$FeedList = $Working->GetFeed();

			if (empty($FeedList)) 
			{
				echo "[INFO] Tidak ditemukan Post, Coba lagi setelah {$delayfeed} detik".PHP_EOL;
				sleep($delayfeed);

				$delayfeed = $delayfeed*rand(2,3);
				$nofeed++;

				continue;
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

				if ($react_comment == 'y') 
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

			if ($temp_post_process < 1) 
			{
				echo "[INFO] Tidak ditemukan Post, Coba lagi setelah {$delayfeed} detik".PHP_EOL;
				sleep($delayfeed);

				$delayfeed = $delayfeed*rand(2,3);
				$nofeed++;

				continue;
			}

		}		

	}
}

Worker::Run();
// use at you own risk