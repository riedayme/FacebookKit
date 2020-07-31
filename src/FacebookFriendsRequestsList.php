<?php namespace Riedayme\FacebookKit;

class FacebookFriendsRequestsList
{

	Const URL = 'https://mbasic.facebook.com/friends/center/requests';
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
			if (strstr($GetDeepURL, 'friends/center/requests')) {				
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

		$FriendsRequests = $xpath->query('//div[@id="friends_center_main"]/div[1]');

		$extract = array();
		if($FriendsRequests->length > 0) 
		{
			foreach ($FriendsRequests as $node) 
			{

				$ProfileList = $xpath->query('//div[@id="friends_center_main"]/div[1]/div/table[@role="presentation"]', $node);

				if($ProfileList->length > 0) 
				{
					foreach ($ProfileList as $key => $profile) {

						$profilelink = $xpath->query('//tr/td[2]/a',$profile)[$key];
						$userid = FacebookHelper::GetStringBetween($profilelink->getAttribute('href'),'uid=','&');

						if ($userid) {
							$username = $profilelink->nodeValue;
							$photo = $xpath->query('//img/@src',$profile)[$key]->value;						
							$linkconfirm = $xpath->query('//tr/td[2]/div[2]/a[1]/@href',$profile)[$key]->value;	
							$linkreject = $xpath->query('//tr/td[2]/div[2]/a[2]/@href',$profile)[$key]->value;

							$extract[] = [
								'userid' => $userid,
								'username' => $username,
								'photo' => $photo,
								'linkconfirm' => "https://mbasic.facebook.com".$linkconfirm,
								'linkreject' => "https://mbasic.facebook.com".$linkreject,
							];

						}

						/* if the results same as limit > break */
						if ($data['limit'] !== false AND count($this->results) >= $data['limit']) break;
					}
				}
			}
		}

		return $extract;
	}
}