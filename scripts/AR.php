<?php  
/**
* Facebook Auto Reaction v1.3
* Last Update 13 Juni 2020
* Author : Faanteyki
*/
require "AUTH.php";

use Riedayme\FacebookKit\FacebookUserGroups;
use Riedayme\FacebookKit\FacebookFeedGroup;
use Riedayme\FacebookKit\FacebookFeedTimeLine;

use Riedayme\FacebookKit\FacebookPostCommentsRead;
use Riedayme\FacebookKit\FacebookPostCommentsReplyRead;

use Riedayme\FacebookKit\FacebookPostReactionSend;

Class FacebookAutoReaction
{

	public $logindata; 
	public $required_access;

	public $targetreaction;
	public $targetreactiontype;
	public $react;

	public $groupid;

	public $delay_bot = 10;
	public $delay_bot_default = 15;
	public $delay_bot_count = 0;	

	public $count_delay;

	public $fileconfig = "./data/ar.json";	
	public $filelog = "./log/ar-%s.json";		

	public function GetInputChoiceTargetReaction() 
	{

		echo "[?] Target Reaction : ".PHP_EOL;
		echo "[1] Feed Timeline".PHP_EOL;
		echo "[2] Feed Group".PHP_EOL;

		echo "[?] Pilihan anda : ";

		$input = trim(fgets(STDIN));

		if (!in_array(strtolower($input),['1','2'])) 
		{
			die("Pilihan tidak diketahui".PHP_EOL);
		}

		return (!$input) ? die('Pilihan masih Kosong'.PHP_EOL) : $input;
	}

	public function GetInputGroupName() {

		echo "[?] Cari Nama Group (karakter): ";

		$input = trim(fgets(STDIN));

		return (!$input) ? die('Nama Group Masih Kosong'.PHP_EOL) : $input;
	}	

	public function GetInputChoiceGroup() {

		echo "[?] Masukan Group yang dipilih (angka): ";

		$input = trim(fgets(STDIN));

		if (strval($input) !== strval(intval($input))) {
			die("Salah memasukan format, pastikan hanya angka".PHP_EOL);
		}

		return $input;
	}	

	public function ChoiceGroup()
	{
		echo "[•] Mendapatkan List Group".PHP_EOL;

		$Group = new FacebookUserGroups();
		$Group->Required($this->required_access);

		$results =$Group->Process();

		echo "[•] Ditemukan ".count($results['response'])." Group".PHP_EOL;

		$search = self::GetInputGroupName();

		$search_results = array();
		foreach ($results['response'] as $key => $group) {
			if (preg_match("/{$search}/i", $group['name'])) {
				$search_results[] = "[{$key}] ".$group['name'].PHP_EOL;
			}
		}

		if (!$search_results) {
			echo "[•] Group tidak ditemukan, coba kembali.".PHP_EOL;
			return self::ChoiceGroup();
		}else{
			echo "[•] Daftar Group yang ditemukan : ".PHP_EOL;			
			echo implode('', $search_results);
		}

		$choice = self::GetInputChoiceGroup();
		return $results['response'][$choice]['id'];
	}	

	public function GetInputReact($data = false) 
	{

		if (strtoupper($data) == 'RANDOM') 
		{
			$reactlist = ['LIKE', 'LOVE', 'CARE', 'WOW'];
			return $reactlist[array_rand($reactlist)];
		}

		echo "[?] Daftar React yang ada [LIKE, LOVE, CARE, HAHA, WOW, SAD, ANGRY, RANDOM]".PHP_EOL;

		echo "[?] Pilihan anda : ";

		$input = strtoupper(trim(fgets(STDIN)));

		$react = ['LIKE', 'LOVE', 'CARE', 'HAHA', 'WOW', 'SAD', 'ANGRY', 'RANDOM'];

		if (!in_array($input,$react)) 
		{
			die("Reaksi Pilihan tidak valid".PHP_EOL);
		}

		return (!$input) ? die('Reaction Masih Kosong'.PHP_EOL) : $input;
	}

	public function GetInputReactComment() 
	{

		echo "[?] React Comment Juga ? (y/n): ";

		$input = trim(fgets(STDIN));

		if (!in_array(strtolower($input),['y','n'])) 
		{
			die("Pilihan tidak diketahui".PHP_EOL);
		}

		return (!$input) ? die('Pilihan masih Kosong'.PHP_EOL) : $input;
	}	

	public function SaveData($data){

		$filename = $this->fileconfig;

		if (file_exists($filename)) {
			$read = file_get_contents($filename);
			$read = json_decode($read,true);
			$dataexist = false;
			foreach ($read as $key => $logdata) {
				if ($logdata['userid'] == $data['userid']) {
					$inputdata[] = $data;
					$dataexist = true;
				}else{
					$inputdata[] = $logdata;
				}
			}

			if (!$dataexist) {
				$inputdata[] = $data;
			}
		}else{
			$inputdata[] = $data;
		}

		return file_put_contents($filename, json_encode($inputdata,JSON_PRETTY_PRINT));
	}

	public function ReadSavedData($userid)
	{

		$filename = $this->fileconfig;

		if (file_exists($filename)) {

			$inputdata = false;
			$read = file_get_contents($filename);
			$read = json_decode($read,TRUE);
			foreach ($read as $key => $logdata) {
				if ($logdata['userid'] == $userid) {
					$inputdata = $logdata;
				}
			}

			return $inputdata;
		}else{
			return false;
		}
	}	

	public function GetFeed()
	{

		echo "[•] Membaca Feed {$this->targetreactiontype}".PHP_EOL;

		if ($this->targetreaction == '2') 
		{
			$Feed = new FacebookFeedGroup();
			$Feed->Required($this->required_access);

			$cursor = false;
			$count = 0;
			$limit = 1;
			$all_data = array();
			do {

				$post = $Feed->Process($this->groupid,$cursor);

				if (!$post['status']) {
					break;
					return false;
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
		}
		elseif ($this->targetreaction == '1') 
		{
			$Feed = new FacebookFeedTimeLine();
			$Feed->Required($this->required_access);

			$cursor = false;
			$count = 0;
			$limit = 1;
			$all_data = array();
			do {

				$post = $Feed->Process($cursor);

				if (!$post['status']) {
					break;
					return false;
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
		}

		echo "[•] Sukses Mendapatkan Feed".PHP_EOL;

		return $all_data;
	}

	public function GetComment($postid)
	{
		$read = new FacebookPostCommentsRead();
		$read->Required($this->required_access);

		$deep = false;
		$count = 0;
		$limit = 1;
		$all_data = array();
		do {

			$post = $read->Process($postid,$deep);

			if (!$post['status']) {			
				break;
				return false;
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
		$readreply->Required($this->required_access);

		$comment_data = array();
		foreach ($all_data as $comment) {

			if ($comment['reply_url']) {

				$deep = false;
				$count = 0;
				$limit = 1;
				do {

					$post = $readreply->Process($comment['reply_url'],$deep);

					if (!$post['status']) {
						break;
						return false;
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

		if (count($comment_data) < 1) {
			return false;
		}

		return $comment_data;
	}

	public function ProcessReact($datapost,$is_comment = false)
	{

		$posttype = ($is_comment == true) ? "Komentar" : "Post";

		$posturl = "https://www.facebook.com/{$datapost['postid']}";

		/* sync react post with log file */
		$sync = self::SyncReact($datapost['postid']);
		if ($sync) 
		{
			echo "[SKIP] React {$posttype} {$posturl} Sudah Diproses.".PHP_EOL;
			return false;
		}

		$type = ($this->react == 'RANDOM') ? self::GetInputReact('RANDOM') : $this->react;

		echo "[•] Proses React {$type} {$datapost['postid']}".PHP_EOL;

		$send = new FacebookPostReactionSend();
		$send->Required($this->required_access);

		$results =$send->Process($datapost['postid'],$type);

		if ($results['status'] != false) 
		{
			echo "[".date('d-m-Y H:i:s')."] Berhasil React {$posttype} {$posturl}".PHP_EOL;
			self::SaveLog($datapost['postid']);
			return true;
		}else{

			if ($results['response'] == 'fail_get_url') 
			{
				echo "[!] Gagal Mendapatkan URL React {$posttype} Pada url {$posturl}".PHP_EOL;

			}elseif ($results['response'] == 'unreact') 
			{
				echo "[SKIP] React {$posttype} {$posturl} Sudah Diproses.".PHP_EOL;	
				self::SaveLog($datapost['postid']);	
			}else{
				echo "[!] Gagal React {$posttype} Pada url {$posturl}".PHP_EOL;
			}
		}

		return false;
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

		$logfilename = sprintf($this->filelog,"{$this->targetreactiontype}{$this->groupid}-{$this->logindata['username']}");
		$log_id = array();
		if (file_exists($logfilename)) 
		{
			$log_id = file_get_contents($logfilename);
			$log_id  = explode(PHP_EOL, $log_id);
		}

		return $log_id;
	}

	public function SaveLog($data)
	{
		return file_put_contents(sprintf($this->filelog,"{$this->targetreactiontype}{$this->groupid}-{$this->logindata['username']}"), $data.PHP_EOL, FILE_APPEND);
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
		$this->count_delay += $this->delay_bot;
		$this->delay_bot = $this->delay_bot+5;
		$this->delay_bot_count++;
	}	

	public function Run()
	{

		echo "Facebook Auto Reaction".PHP_EOL;

		$login = new Auth();

		$this->logindata = $login->Run();
		$this->required_access = [
			'cookie' => $this->logindata['cookie'],
			'access_token' => $this->logindata['access_token'],
			'useragent' => false, //  false for auto genereate
			'proxy' => false // false for not use proxy 
		];

		if ($check = self::ReadSavedData($this->logindata['userid'])){
			echo "[?] Anda Memiliki konfigurasi yang tersimpan, gunakan kembali (y/n) : ";

			$reuse = trim(fgets(STDIN));

			if (!in_array(strtolower($reuse),['y','n'])) 
			{
				die("Pilihan tidak diketahui");
			}

			if ($reuse == 'y') {

				$this->targetreaction = $check['targetreaction'];
				if ($this->targetreaction == '2') {
					$this->targetreactiontype = 'Group';
					$this->groupid = $check['groupid'];
				}else{
					$this->targetreactiontype = 'Timeline';
				}
				$this->react = $check['react'];
				$react_comment =$check['react_comment'];

			}else{

				$this->targetreaction = self::GetInputChoiceTargetReaction();
				if ($this->targetreaction == '2') {
					$this->targetreactiontype = 'Group';
					$this->groupid = self::ChoiceGroup();
				}else{
					$this->targetreactiontype = 'Timeline';
				}
				$this->react = self::GetInputReact();
				$react_comment = self::GetInputReactComment();

				$save_config = [
					'userid' => $this->logindata['userid'],
					'username' => $this->logindata['username'],
					'targetreaction' => $this->targetreaction,
					'react' => $this->react,					
					'react_comment' => $react_comment
				];

				if ($this->targetreaction == '2') {
					$save_config = array_merge($save_config,['groupid' => $this->groupid]);
				}

				/* save new config data */
				self::SaveData($save_config);
			}
		}else{

			$this->targetreaction = self::GetInputChoiceTargetReaction();
			if ($this->targetreaction == '2') {
				$this->targetreactiontype = 'Group';
				$this->groupid = self::ChoiceGroup();
			}else{
				$this->targetreactiontype = 'Timeline';
			}
			$this->react = self::GetInputReact();
			$react_comment = self::GetInputReactComment();

			$save_config = [
				'userid' => $this->logindata['userid'],
				'username' => $this->logindata['username'],
				'targetreaction' => $this->targetreaction,
				'react' => $this->react,					
				'react_comment' => $react_comment
			];

			if ($this->targetreaction == '2') {
				$save_config = array_merge($save_config,['groupid' => $this->groupid]);
			}

			/* save new config data */
			self::SaveData($save_config);
		}

		while (true) 
		{

			$FeedList = self::GetFeed();

			$no_activity = true;
			foreach ($FeedList as $post) 
			{

				$process_post = self::ProcessReact($post);

				if ($process_post) 
				{
					/* delay bot */
					self::DelayBot();

					$no_activity = false; /* activty detected */
				}

				if ($react_comment == 'y') 
				{
					echo "[•] Membaca Komentar pada post {$post['postid']}".PHP_EOL;

					$comments = self::GetComment($post['postid']);
					if (!$comments) 
					{
						echo "[•] Tidak ada komentar pada post {$post['postid']}".PHP_EOL;
						continue;
					}

					foreach ($comments as $comment) 
					{

						$commentpost['postid'] = $comment['commentid'];
						$process_comment = self::ProcessReact($commentpost,true);

						if ($process_comment) 
						{
							/* delay bot */
							self::DelayBot();

							$no_activity = false; /* activty detected */
						}

						/* process react reply comment if exist */
						if ($comment['reply']) {
							
							foreach ($comment['reply'] as $commentreply) {

								$commentreply['postid'] = $commentreply['commentid'];
								$process_comment = self::ProcessReact($commentreply,true);

								if ($process_comment) 
								{
									/* delay bot */
									self::DelayBot();

									$no_activity = false; /* activty detected */
								}
							}

						}

					}
				}
			}

			if ($no_activity) 
			{
				echo "[•] Tidak ditemukan Post, Coba lagi setelah {1000} detik".PHP_EOL;
				sleep(1000);
				continue;
			}

		}		

	}	
}

$x = new FacebookAutoReaction();
$x->Run();
// use at you own risk