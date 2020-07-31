<?php namespace Riedayme\FacebookKit;

class FacebookPostReactionUserList
{

	Const URL = 'https://mbasic.facebook.com/ufi/reaction/profile/browser/?ft_ent_identifier=%s';
	Const URLDEEP = 'https://mbasic.facebook.com/%s';

	public $cookie;
	public $useragent;		
	public $proxy;

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

	}

	public function Process($postid,$deep = false)
	{

		if ($deep) {
			$url = sprintf(self::URLDEEP,$deep);
		}else{
			$url = sprintf(self::URL,$postid);
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

			$GetDeepURL = $xpath->query('//div/ul/li[last()]/table/tbody/tr/td/div/a/@href');

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

		$ProfileXpath = $xpath->query('//div/ul/li/table/tbody/tr/td/table/tbody');

		$extract = array();
		if($ProfileXpath->length > 0) 
		{
			foreach ($ProfileXpath as $key => $node) 
			{
				$xpathlinkaction = $xpath->query('tr/td[4]/div/a/@href',$node);

				/* skip if tag a not exist */
				if (is_null($xpathlinkaction[0])) continue;

				$linkaction = $xpathlinkaction[0]->value;

				if (strstr($linkaction, 'friends/add_friend.php')) {

					$profilelink = $xpath->query('//table/tbody/tr/td/table/tbody/tr/td[3]/header/h3[1]/a',$node)[$key];
					$userlink = $profilelink->getAttribute('href');
					$username = $profilelink->nodeValue;
					$photo = $xpath->query('//table/tbody/tr/td/table/tbody/tr/td[1]/img/@src',$node)[$key]->value;

					$extract[] = [
						'userlink' => $userlink,
						'username' => $username,
						'photo' => $photo,
						'sendrequest' => "https://mbasic.facebook.com".$linkaction,
					];
				}
			}
		}

		return $extract;
	}
}