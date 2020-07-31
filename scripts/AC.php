<?php  
/**
* Facebook Auto Confirm Friends Requests v1.0
* Last Update 17 Juni 2020
* Author : Faanteyki
*/
require "../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookAuth;
use Riedayme\FacebookKit\FacebookChecker;

use Riedayme\FacebookKit\FacebookFriendsConfirmRequest;

use Riedayme\FacebookKit\FacebookFriendsRequests;

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

		$CheckPreviousCookie = FacebookAutoConfirm::CheckPreviousCookie();

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
}

Class FacebookAutoConfirm
{

	public $login_data;
	public $cookie;
	public $access_token;	
	public $username;

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

	public function GetRequests()
	{

		echo "[INFO] Mengambil data pertemanan {$this->username}".PHP_EOL;

		$FriendsRequests = new FacebookFriendsRequests();
		$FriendsRequests->SetCookie($this->cookie);
		$FriendsRequests->GetFriendsRequests([
			'limit' => false
			]);

		$results = $FriendsRequests->results();

		return $results;
	}

	public function ConfirmRequest($data)
	{

		$Send = new FacebookFriendsConfirmRequest();
		$Send->SetCookie($this->cookie);

		echo "[INFO] Proses Konfirmasi pertemanan dengan {$data['username']}".PHP_EOL;		

		$process = $Send->Process($data['linkconfirm']);

		if ($process != false) 
		{
			echo "[SUCCESS] Menerima pertemanan https://facebook.com/{$data['userid']}".PHP_EOL;
			return true;
		}else{
			echo "[FAILED] Gagal Pertemanan dengan user {$data['username']}, Kesalahan pada kode.".PHP_EOL;
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

		echo " --- Facebook Auto Confirm v1.0 ---".PHP_EOL;

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

		$delayrequest_default = 10;
		$delayrequest = 10;

		$delay_default = 10;
		$delay = 10;

		/* Call Class */
		$Working = new FacebookAutoConfirm();
		$Working->Auth($data);

		$norequests = 0;
		$confirmrequest = 0;
		while (true) 
		{

			/* when norequest 5 reset sleep value to default */
			if ($norequests >= 5) 
			{
				$delayrequest = $delayrequest_default;
				$norequests = 0;
			}

			$RequestsList = $Working->GetRequests();

			if (empty($RequestsList)) 
			{
				echo "[INFO] Tidak ditemukan request pertemanan, Coba lagi setelah {$delayrequest} detik".PHP_EOL;
				sleep($delayrequest);

				$delayrequest = $delayrequest*rand(2,3);
				$norequests++;
				continue;
			}

			foreach ($RequestsList as $key => $request) 
			{

				/* when confirmrequest 5 reset sleep value to default */
				if ($confirmrequest >= 5) 
				{
					$delay = $delay_default;
					$confirmrequest = 0;
				}	

				$process = $Working->ConfirmRequest($request);

				if ($process) 
				{
					echo "[INFO] Delay {$delay}".PHP_EOL;
					sleep($delay);
					$delay = $delay+5;
					$confirmrequest++;
				}
			}

		}		

	}
}

Worker::Run();
// use at you own risk