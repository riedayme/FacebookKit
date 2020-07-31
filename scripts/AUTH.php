<?php  
/**
* Facebook Auth v1.0
* Last Update 31 Juli 2020
* Author : Faanteyki
*/
require "../vendor/autoload.php";

date_default_timezone_set('Asia/Jakarta');

use Riedayme\FacebookKit\FacebookAuthCookie;
use Riedayme\FacebookKit\FacebookChecker;

Class Auth 
{

	public $filesavedata = './data/user.json';

	public function GetInputChoiceLogin() 
	{

		echo "[?] Pilihan Login Menggunakan : ".PHP_EOL;
		echo "[c] Masuk menggunakan cookie".PHP_EOL;
		echo "[p] Masuk menggunakan username dan password".PHP_EOL;

		echo "[?] Pilihan anda : ";

		$input = trim(fgets(STDIN));

		if (!in_array(strtolower($input),['c','p'])) 
		{
			die("[!] Pilihan tidak diketahui".PHP_EOL);
		}

		return $input;
	}

	public function GetInputCookie() 
	{

		echo "[?] Masukan Cookie : ";

		$input = trim(fgets(STDIN));

		return $input;
	}	

	public function GetInputUsername() 
	{

		echo "[?] Masukan Username : ";

		$input = trim(fgets(STDIN));

		return $input;
	}	

	public function GetInputPassword() 
	{

		echo "[?] Masukan Password : ";

		/** 
		 * hidden password
		 * https://gist.github.com/scribu/5877523
		 */			
		echo "\033[30;40m";  
		$input = trim(fgets(STDIN));
		echo "\033[0m";     

		return $input;
	}

	public function SaveData($data){

		$filename = $this->filesavedata;

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

	public function ReadSavedData()
	{

		$filename = $this->filesavedata;

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

	public function ReadData($data)
	{

		$filename = $this->filesavedata;

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

	public function New_Login_Cookie($cookie)
	{
		echo "[•] Validate Cookie".PHP_EOL;

		$auth = new FacebookAuthCookie();
		$results =$auth->Login($cookie);

		if (!$results['status']) 
		{
			die($results['response']);
		}

		$results = $results['response'];

		echo "[•] Menyimpan Data Login".PHP_EOL;

		self::SaveData($results);

		return $results;
	}

	public function New_Login($username,$password)
	{

		// no process
	}

	public function Old_Login($key) 
	{

		$results = self::ReadData($key);

		echo "[•] Check Live Cookie".PHP_EOL;

		$check_cookie = FacebookChecker::CheckLiveCookie($results['cookie']);

		if (!$check_cookie['status']) 
		{
			die($check_cookie['response']);
		}

		return $results;
	}

	public function Run($reauth = false)
	{

		if ($reauth) 
		{
			return self::New_Login($reauth['username'],$reauth['password']);
		}
		elseif ($check = self::ReadSavedData() AND !$reauth) 
		{

			echo "[?] Anda Memiliki Cookie yang tersimpan pilih angkanya dan gunakan kembali : ".PHP_EOL;

			foreach ($check as $key => $cookie) 
			{
				echo "[{$key}] ".$cookie['username'].PHP_EOL;

				$data_cookie[] = $key;
			}

			echo "[x] Masuk menggunakan akun baru".PHP_EOL;

			echo "[?] Pilihan Anda : ";

			$input = strtolower(trim(fgets(STDIN)));			

			if ($input != 'x') 
			{

				if (strval($input) !== strval(intval($input))) 
				{
					die("Salah memasukan format, pastikan hanya angka");
				}

				if (!in_array($input, $data_cookie)) 
				{
					die("Pilihan tidak ditemukan");
				}

				return self::Old_Login($input);

			}else{

				$login_choice = self::GetInputChoiceLogin();

				if ($login_choice == 'c') {
					$cookie = self::GetInputCookie();

					return self::New_Login_Cookie($cookie);
				}else{
					$username = self::GetInputUsername();
					$password = self::GetInputPassword();

					return self::New_Login($username,$password);
				}
			}
		}else{

			$login_choice = self::GetInputChoiceLogin();

			if ($login_choice == 'c') {
				$cookie = self::GetInputCookie();

				return self::New_Login_Cookie($cookie);
			}else{
				$username = self::GetInputUsername();
				$password = self::GetInputPassword();

				return self::New_Login($username,$password);
			}


		}
	}
}