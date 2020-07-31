<?php namespace Riedayme\FacebookKit;

class FacebookPostReactionSendTouch
{

	Const URL = 'https://touch.facebook.com/ufi/reaction/';

	public $cookie;
	public $useragent;		
	public $proxy;

	public $fb_dtsg;

	public function Required($data) 
	{

		if (!$data['cookie']) {
			die('cookie Empty');
		}

		$this->cookie = $data['cookie'];

		if (!$data['useragent']) {
			$this->useragent = FacebookUserAgent::GenerateStatic();
		}else{
			$this->useragent = $data['useragent'];
		}

		if (!$data['proxy']) {
			$this->proxy = false;
		}else{
			$this->proxy = $data['proxy'];
		}

		$get_dstg = FacebookFormRequired::GetFbDTSG($this);

		if (!$get_dstg['status']) {
			die($get_dstg['response']);
		}

		$this->fb_dtsg = $get_dstg['response'];
	}	

	public function Process($postid,$reaction)
	{

		$reaction_type = self::ConvertReact($reaction);
		$postdata = "reaction_type={$reaction_type}&ft_ent_identifier={$postid}&fb_dtsg={$this->fb_dtsg}";

		$headers = array();
		$headers[] = 'User-Agent: '.$this->useragent;
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Cookie: '.$this->cookie;

		$access = FacebookHelper::curl(self::URL, $postdata , $headers , false, false, $this->proxy);

		$response = $access['header'];

		if (strstr($response, 'login.php')) {
			return [
				'status' => false,
				'response' => 'not_login'
			];
		}else{
			return [
				'status' => true,
				'response' => 'success_react'
			];
		}
	}

	public function ConvertReact($data)
	{
		$type = false;
		if ($data == 'LIKE') {
			$type = '1';
		}elseif ($data == 'LOVE') {
			$type = '2';
		}elseif ($data == 'CARE') {
			$type = '1';
		}elseif ($data == 'HAHA') {
			$type = '4';
		}elseif ($data == 'WOW') {
			$type = '3';
		}elseif ($data == 'SAD') {
			$type = '7';
		}elseif ($data == 'ANGRY') {
			$type = '8';
		}elseif ($data == 'UNREACT') {
			$type = '0';
		}

		return $type;
	}

}