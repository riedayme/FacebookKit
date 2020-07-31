<?php namespace Riedayme\FacebookKit;

class FacebookPostCommentsRead
{

	Const URL = 'https://mbasic.facebook.com/%s';

	public $cookie;
	public $useragent;		
	public $proxy;

	public $postid;

	public function Required($data) 
	{

		if (!$data['cookie']) 
		{
			die('cookie Empty');
		}

		$this->cookie = $data['cookie'];

		if (!$data['useragent']) 
		{
			$this->useragent = FacebookUserAgent::GenerateStatic();
		}else{
			$this->useragent = $data['useragent'];
		}

		if (!$data['proxy']) 
		{
			$this->proxy = false;
		}else{
			$this->proxy = $data['proxy'];
		}
	}

	public function Process($postid,$deep = false)
	{

		if ($deep) {
			$url = sprintf(self::URL,$deep);
		}else{
			$url = sprintf(self::URL,$postid);
		}
		
		$headers = array();
		$headers[] = 'User-Agent: '.$this->useragent;
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Cookie: '.$this->cookie;	

		$access = FacebookHelper::curl($url, false , $headers , false, false, $this->proxy);

		if (strstr($access['header'], 'login.php')) 
		{
			return [
				'status' => false,
				'response' => 'not_login'
			];
		}else{

			$dom = FacebookHelper::GetDom($access['body']);
			$xpath = FacebookHelper::GetXpath($dom);

			$GetDeepURL = $xpath->query('//div[@id="ufi_'.$postid.'"]/div/div[4]/div[contains(@id,"see_prev")]/a/@href');

			$deep = false;
			if ($GetDeepURL->length > 0) {
				$deep = $GetDeepURL[0]->value;
			}

			$this->postid = $postid;

			return [
				'status' => true,
				'response' => $xpath,
				'deep' => $deep
			];
		}
	}

	public function Extract($response)
	{

		$xpath = $response['response'];

		$XpathCommentList = $xpath->query('//div[@id="ufi_'.$this->postid.'"]/div/div[4]/div');

		$extract = array();
		if($XpathCommentList->length > 0) 
		{

			foreach ($XpathCommentList as $key => $node) 
			{

				$commentid = $node->getAttribute('id');

				if (is_numeric($commentid)) {

					$build_commentid = "{$this->postid}_{$commentid}";
					$profilexpath = $xpath->query('//div[@id="'.$commentid.'"]/div/h3/a',$node)[0];
					$username = $profilexpath->nodeValue;
					$userid = $profilexpath->getAttribute('href');

					$CheckReplyTag = $xpath->query('//div[contains(@id,"'.$build_commentid.'")]/div/a',$node);

					$reply = false;
					if ($CheckReplyTag->length > 0) {
						$reply = $CheckReplyTag[0]->getAttribute('href');
					}

					$extract[] = [
						'userid' => $userid,					
						'username' => $username,
						'commentid' => $build_commentid,
						'reply_url' => $reply
					];

				}

			}
		}

		return $extract;
	}

}