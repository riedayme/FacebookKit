<?php namespace Riedayme\FacebookKit;

class FacebookFriendsSendRequest
{

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

	public function Process($url,$postdata = false)
	{

		$headers = array();
		$headers[] = 'Authority: mbasic.facebook.com';
		$headers[] = 'Upgrade-Insecure-Requests: 1';
		$headers[] = 'User-Agent: '.$this->useragent;
		$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9';
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Sec-Fetch-Site: none';
		$headers[] = 'Sec-Fetch-Mode: navigate';
		$headers[] = 'Accept-Language: en-US,en;q=0.9,id;q=0.8';
		$headers[] = 'Cookie: '.$this->cookie;	

		$access = FacebookHelper::curl($url, $postdata , $headers , false, false, $this->proxy);

		$response = $access['header'];

		if (strstr($response, 'login.php')) {
			return [
				'status' => false,
				'response' => 'not_login'
			];
		}else{

			if (strstr($response, '200 OK') AND strstr($response, '302 Found') OR strstr($response, 'HTTP/2 200') AND strstr($response, 'HTTP/2 302')) 
			{
				return [
					'status' => true,
					'response' => 'success_request'
				];
			}
			elseif (strstr($response, '200 OK') OR strstr($response, 'HTTP/2 200')) {

				/* if user not identified request 2x for confirm */

				$data['url'] = $url;
				$data['cookie'] = $this->cookie;
				$data['useragent'] = $this->useragent;				
				$getpostdata = FacebookFormRequired::SendRequestFriendship($data);

				$postdata['fb_dtsg'] = $getpostdata['fb_dtsg'];
				$postdata['jazoest'] = $getpostdata['jazoest'];
				$postdata['_wap_notice_shown'] = $getpostdata['_wap_notice_shown'];
				$postdata['_orig_post_vars'] = $getpostdata['_orig_post_vars'];						

				$postdata = http_build_query($postdata);

				self::Process($url,$postdata);
			}else{
				return [
					'status' => false,
					'response' => 'failed_request'
				];
			}

		}

	}
}