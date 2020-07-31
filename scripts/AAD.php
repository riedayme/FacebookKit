<?php  
/**
* Facebook Auto Add Friends v1.0
* Last Update 18 Juni 2020
* Author : Faanteyki
*/
require "../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookAuth;
use Riedayme\FacebookKit\FacebookChecker;

use Riedayme\FacebookKit\FacebookFriendsSendRequest;

use Riedayme\FacebookKit\FacebookFriendsSuggest;
use Riedayme\FacebookKit\FacebookPostReactionProfile;

Class InputHelper
{

	public function GetInputChoiceLogin($setdata = false) 
	{

		if ($setdata) return $setdata;

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

	public function GetInputCookie($setdata = false) {

		if ($setdata) return $setdata;

		$CheckPreviousCookie = FacebookAutoAddFriends::CheckPreviousCookie();

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

	public function GetInputUsername($setdata = false) 
	{

		if ($setdata) return $setdata;

		echo "Masukan Username : ".PHP_EOL;

		$input = trim(fgets(STDIN));

		return (!$input) ? die('Username Masih Kosong'.PHP_EOL) : $input;
	}

	public function GetInputPassword($setdata = false) 
	{

		if ($setdata) return $setdata;

		echo "Masukan Password: ".PHP_EOL;

		return trim(fgets(STDIN));
	}

	public function GetInputChoiceExtractUser($data = false) 
	{

		if ($data) return $data;

		echo "Pilihan Cara Extract User : ".PHP_EOL;
		echo "[1] Extract dari Suggest".PHP_EOL;
		echo "[2] Extract dari Reaction Post".PHP_EOL;

		$input = trim(fgets(STDIN));

		if (!in_array(strtolower($input),['1','2'])) 
		{
			die("Pilihan tidak diketahui".PHP_EOL);
		}

		return (!$input) ? die('Pilihan masih Kosong'.PHP_EOL) : $input;
	}		

	public function GetInputPostID($setdata = false) 
	{

		if ($setdata) return $setdata;

		echo "Masukan ID Postingan (number) : ".PHP_EOL;

		$input = trim(fgets(STDIN));

		if (strval($input) !== strval(intval($input))) 
		{
			die("Salah memasukan format, pastikan hanya angka".PHP_EOL);
		}

		return (!$input) ? die('ID Postingan masih kosong'.PHP_EOL) : $input;
	}					
}

Class FacebookAutoAddFriends
{

	public $login_data;
	public $cookie;
	public $access_token;	
	public $username;

	public $extractuser;
	public $postid;

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

		$this->extractuser = InputHelper::GetInputChoiceExtractUser();

		if ($this->extractuser == '2') {
			$this->postid = InputHelper::GetInputPostID();
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

	public function GetProfile()
	{

		if ($this->extractuser == '2') 
		{

			echo "[INFO] mengambil profile dari post {$this->postid}".PHP_EOL;

			$ReactionProfile = new FacebookPostReactionProfile();
			$ReactionProfile->SetCookie($this->cookie);

			$ReactionProfile->GetReactionProfile([
				'postid' => $this->postid,
				'limit' => 5
				]);

			$results = $ReactionProfile->results();
		}elseif ($this->extractuser == '1') 
		{

			echo "[INFO] mengambil profile dari suggest".PHP_EOL;

			$FriendsSuggest = new FacebookFriendsSuggest();
			$FriendsSuggest->SetCookie($this->cookie);

			$FriendsSuggest->GetFriendsSuggest([
				'limit' => 5
				]);

			$results = $FriendsSuggest->results();
		}

		return $results;
	}

	public function SendRequest($data)
	{

		if ($this->extractuser == '1') {
			$data['userlink'] = "/".$data['userid'];
		}

		echo "[INFO] Proses kirim pertemanan dengan {$data['username']}".PHP_EOL;		

		$Send = new FacebookFriendsSendRequest();
		$Send->SetCookie($this->cookie);

		$process = $Send->Process($data['linksendrequest']);

		if ($process != false) 
		{
			echo "[SUCCESS] Kirim pertemanan https://facebook.com{$data['userlink']}".PHP_EOL;
			return true;
		}else{
			echo "[FAILED] Gagal Kirim Pertemanan dengan user https://facebook.com{$data['userlink']}".PHP_EOL;
		}

		return false;
	}

	public function ReAuth()
	{

		echo "[INFO] Mencoba login kembali".PHP_EOL;
		self::Auth($this->login_data,true);
	}
}

Class Worker
{
	public function Run()
	{

		echo " --- Facebook Auto Add Friends v1.0 ---".PHP_EOL;

		$login_choice = InputHelper::GetInputChoiceLogin();

		//$login_choice;

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

		$delayrequest_default = 10;
		$delayrequest = 10;

		$delay_default = 10;
		$delay = 10;

		/* Call Class */
		$Working = new FacebookAutoAddFriends();
		$Working->Auth($data);

		$norequests = 0;
		$sendrequest = 0;
		while (true) 
		{

			/* when norequest 5 reset sleep value to default */
			if ($norequests >= 5) 
			{
				$delayrequest = $delayrequest_default;
				$norequests = 0;
			}

			$ProfileList = $Working->GetProfile();


			if (empty($ProfileList)) 
			{
				echo "[INFO] Tidak ditemukan profile list, Coba lagi setelah {$delayrequest} detik".PHP_EOL;
				sleep($delayrequest);

				$delayrequest = $delayrequest*rand(2,3);
				$norequests++;
				continue;
			}

			foreach ($ProfileList as $key => $profile) 
			{

				/* when confirmrequest 5 reset sleep value to default */
				if ($sendrequest >= 5) 
				{
					$delay = $delay_default;
					$sendrequest = 0;
				}	

				$process = $Working->SendRequest($profile);

				if ($process) 
				{
					echo "[INFO] Delay {$delay}".PHP_EOL;
					sleep($delay);
					$delay = $delay+5;
					$sendrequest++;
				}
			}

		}		

	}
}

Worker::Run();
// use at you own risk