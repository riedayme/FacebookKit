<?php namespace Riedayme\FacebookKit;

class FacebookPostCommentsReplyRead
{

	Const URL = 'https://mbasic.facebook.com/%s';

	public $cookie;
	public $useragent;		
	public $proxy;

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

	public function Process($url,$deep = false)
	{

		if ($deep) {
			$url = sprintf(self::URL,$deep);
		}else{
			$url = sprintf(self::URL,$url);
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

			$GetDeepURL = $xpath->query('/html/body/div/div/div[2]/div/div[1]/div[2]/div[1]/a/@href');

			$deep = false;
			if ($GetDeepURL->length > 0) {
				$deep = $GetDeepURL[0]->value;
			}

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

		$XpathCommentList = $xpath->query('//div[@id="objects_container"]/div/div[1]/div[2]/div');

		$extract = array();
		if($XpathCommentList->length > 0) 
		{

			foreach ($XpathCommentList as $ked => $node) 
			{

				$commentid = $node->getAttribute('id');

				if (is_numeric($commentid)) {
					$build_commentid = "{$commentid}";
					$profilexpath = $xpath->query('//div[@id="'.$commentid.'"]/div/h3/a',$node)[0];
					$username = $profilexpath->nodeValue;
					$userid = $profilexpath->getAttribute('href');

					$extract[] = [
						'userid' => $userid,					
						'username' => $username,
						'commentid' => $build_commentid
					];
				}
			}
		}

		return $extract;
	}

}