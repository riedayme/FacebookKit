<?php namespace Riedayme\FacebookKit;

class FacebookUserGroups
{

	Const URL = 'https://mbasic.facebook.com/groups/?seemore&refid=27';

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

	public function Process()
	{
		
		$headers = array();
		$headers[] = 'User-Agent: '.$this->useragent;
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Cookie: '.$this->cookie;	

		$access = FacebookHelper::curl(self::URL, false , $headers , false, false, $this->proxy);

		if (strstr($access['header'], 'login.php')) 
		{
			return [
				'status' => false,
				'response' => 'not_login'
			];
		}

		$response = $access['body'];

		$dom = FacebookHelper::GetDom($response);
		$xpath = FacebookHelper::GetXpath($dom);

		$GroupList = $xpath->query('/html/body/div/div/div[2]/div/table');

		$extract = array();
		if($GroupList->length > 0) 
		{
			foreach ($GroupList as $node) 
			{
				$GroupLink = $xpath->query('//td[@class="u"]/a', $node);

				if($GroupLink->length > 0) 
				{
					foreach ($GroupLink as $link) {

						$id = $link->getAttribute('href');
						$id = FacebookHelper::GetStringBetween($id,'/groups/','?refid=27');
						$name = $link->nodeValue;
						$url = "https://facebook.com/groups/{$id}";

						$extract[] = [
							'id' => $id,
							'name' => $name,
							'url' => $url
						];
					}
				}
			}
		}else{
			return [
				'status' => false,
				'response' => 'groups_not_found'
			];
		}

		if (count($extract) > 0) {
			return [
				'status' => true,
				'response' => $extract
			];
		}

		return [
			'status' => false,
			'response' => 'fail_get_data'
		];
	}
}