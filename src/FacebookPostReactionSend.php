<?php namespace Riedayme\FacebookKit;

class FacebookPostReactionSend
{

	Const URL = 'https://mbasic.facebook.com/reactions/picker/?is_permalink=1&ft_id=%s';

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

	public function Process($postid,$reaction)
	{

		$url = self::GetReactionURL($postid,$reaction);

		if (!$url['status']) 
		{
			return [
				'status' => false,
				'response' => $url['response']
			];
		}

		$headers = array();
		$headers[] = 'User-Agent: '.$this->useragent;
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Cookie: '.$this->cookie;

		$access = FacebookHelper::curl($url['response'], false , $headers , false, false, $this->proxy);

		$response = $access['header'];

		if (strstr($response, 'login.php')) 
		{
			return [
				'status' => false,
				'response' => 'not_login'
			];
		}else{

			if (strstr($response, '200 OK') AND strstr($response, '302 Found') OR strstr($response, 'HTTP/2 200') AND strstr($response, 'HTTP/2 302')) 
			{
				$status = true;
			}else{
				$status = false;
			}

			return [
				'status' => $status,
				'response' => 'success_react'
			];
		}

	}

	public function GetReactionURL($postid,$reaction)
	{

		$url = sprintf(self::URL,$postid);

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
		}

		$response = $access['body'];

		$dom = FacebookHelper::GetDom($response);
		$xpath = FacebookHelper::GetXpath($dom);

		$XpathReactlionlist = $xpath->query('//li/table/tbody/tr/td/a/@href');

		if($XpathReactlionlist->length > 0) 
		{
			$reaction_data = array();
			foreach ($XpathReactlionlist as $node) 
			{
				$url = FacebookHelper::InnerHTML($node);
				$url = "https://mbasic.facebook.com".$url;

				if (!strstr($url, '/story.php')) 
				{

					$type = self::ConvertReact($url);

					$reaction_data[$type] = html_entity_decode(trim($url));
				}
			}

			if ((!empty($reaction_data[$reaction]))) 
			{
				return [
					'status' => true,
					'response' => $reaction_data[$reaction]
				];
			}else{
				return [
					'status' => false,
					'response' => 'unreact'
				];
			}
		}

		return [
			'status' => false,
			'response' => 'fail_get_url'
		];
	}

	public function ConvertReact($url)
	{

		$type = false;
		if (strstr($url, 'reaction_type=1&')) 
		{
			$type = 'LIKE';
		}elseif (strstr($url, 'reaction_type=2&')) 
		{
			$type = 'LOVE';
		}elseif (strstr($url, 'reaction_type=16&')) 
		{
			$type = 'CARE';
		}elseif (strstr($url, 'reaction_type=4&')) 
		{
			$type = 'HAHA';
		}elseif (strstr($url, 'reaction_type=3&')) 
		{
			$type = 'WOW';
		}elseif (strstr($url, 'reaction_type=7&')) 
		{
			$type = 'SAD';
		}elseif (strstr($url, 'reaction_type=8&')) 
		{
			$type = 'ANGRY';
		}elseif (strstr($url, 'reaction_type=0&')) 
		{
			$type = 'UNREACT';
		}

		return $type;
	}

}