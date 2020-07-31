<?php namespace Riedayme\FacebookKit;

class FacebookFriendsSuggest
{

	Const URL = 'https://mbasic.facebook.com/friends/center/suggestions/';
	Const URLDEEP = 'https://mbasic.facebook.com/%s';

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

	public function Process($deep = false)
	{

		if ($deep) {
			$url = sprintf(self::URLDEEP,$deep);
		}else{
			$url = self::URL;
		}
		
		$headers = array();
		$headers[] = 'User-Agent: '.$this->useragent;
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Cookie: '.$this->cookie;	

		$access = FacebookHelper::curl($url, false , $headers , false, false, $this->proxy);

		$response = $access['header'];

		if (strstr($response, 'login.php')) {
			return [
				'status' => false,
				'response' => 'not_login'
			];
		}else{

			$dom = FacebookHelper::GetDom($access['body']);
			$xpath = FacebookHelper::GetXpath($dom);

			$GetDeep = $xpath->query('//div[@id="friends_center_main"]/div[2]/a/@href');

			$GetDeepURL = $GetDeep[0]->value;

			$deep = false;
			if (strstr($GetDeepURL, 'friends/center/suggestions')) {				
				$deep = $GetDeepURL;
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

		$FriendsSuggest = $xpath->query('//div[@id="friends_center_main"]/div[1]');

		$extract = array();
		if($FriendsSuggest->length > 0) 
		{
			foreach ($FriendsSuggest as $node) 
			{

				$ProfileList = $xpath->query('//div[@id="friends_center_main"]/div[1]/div/table[@role="presentation"]', $node);

				if($ProfileList->length > 0) 
				{
					foreach ($ProfileList as $key => $profile) {

						$profilelink = $xpath->query('//tr/td[2]/a',$profile)[$key];

						$userid = FacebookHelper::GetStringBetween($profilelink->getAttribute('href'),'uid=','&');

						if ($userid) {
							$username = $profilelink->nodeValue;
							$photo = $xpath->query('//tr/td[1]/img/@src',$profile)[$key]->value;						
							$sendrequest = $xpath->query('//tr/td[2]/div[2]/table/tbody/tr/td/div[1]/a/@href',$profile)[$key]->value;	

							$extract[] = [
							'userid' => $userid,
							'username' => $username,
							'photo' => $photo,
							'sendrequest' => "https://mbasic.facebook.com".$sendrequest,
							];

						}

					}
				}
			}
		}

		return $extract;
	}
}