<?php  
/**
* Facebook Mass Delete Post
* Last Update 31 Juli 2020
* Author : Faanteyki
*/
require "AUTH.php";

use Riedayme\FacebookKit\FacebookUserPost;
use Riedayme\FacebookKit\FacebookPostDelete;

Class FacebookMassDeletePost
{

	public $logindata; 
	public $required_access;

	public $cursor = false;

	public $delay_bot = 10;
	public $delay_bot_default = 15;
	public $delay_bot_count = 0;

	public function GetPost()
	{

		echo "[•] Membaca Post".PHP_EOL;

		$Feed = new FacebookUserPost();
		$Feed->Required($this->required_access);

		$post = $Feed->Process($this->logindata['userid'],$this->cursor);

		if (!$post['status']) {
			return false;
		}

		if ($post['cursor']) {
			$this->cursor = $post['cursor'];
		}else{
			$this->cursor = false;
		}

		$data = $Feed->Extract($post);

		return $data;
	}

	public function DeletePost($post)
	{

		echo "[•] Proses Hapus Post {$post['postid']}".PHP_EOL;

		$send = new FacebookPostDelete();
		$send->Required($this->required_access);

		return $send->Process($post['userid'],$post['postid']);
	}

	public function DelayBot()
	{

		/* reset sleep value to default */
		if ($this->delay_bot_count >= 5) {
			$this->delay_bot = $this->delay_bot_default;
			$this->delay_bot_count = 0;
		}	

		echo "[•] Delay {$this->delay_bot}".PHP_EOL;
		sleep($this->delay_bot);
		$this->delay_bot = $this->delay_bot+5;
		$this->delay_bot_count++;
	}

	public function Run()
	{

		echo "Facebook Mass Delete Post".PHP_EOL;

		$login = new Auth();

		$this->logindata = $login->Run();
		$this->required_access = [
			'cookie' => $this->logindata['cookie'],
			'access_token' => $this->logindata['access_token'],
			'useragent' => false, //  false for auto genereate
			'proxy' => false // false for not use proxy 
		];

		do {

			$postlist = self::GetPost();

			foreach ($postlist as $post) {
				$deletepost = self::DeletePost($post);		

				if ($deletepost['status']) {
					echo "[•] Berhasil Menghapus Post {$post['postid']}".PHP_EOL;
				}else{
					echo "[•] Gagal Menghapus Post {$post['postid']}".PHP_EOL;
				}

				/* delay bot */
				self::DelayBot();
			}
			
		} while ($this->cursor !== false);

	}

}

$x = new FacebookMassDeletePost();
$x->Run();
// use at you own risk