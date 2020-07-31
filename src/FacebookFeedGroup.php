<?php namespace Riedayme\FacebookKit;

class FacebookFeedGroup
{

	public $access_token;
	public $useragent;		
	public $proxy;

	public function Required($data) 
	{

		if (!$data['access_token']) {
			die('access_token Empty');
		}

		$this->access_token = $data['access_token'];

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
	public function Process($groupid,$cursor = false)
	{

		if ($cursor) {
			$url = $cursor;
		}else{
			$url ="https://graph.facebook.com/{$groupid}/feed?fields=id&limit=5&access_token={$this->access_token}";
		}

		$headers = array();
		$headers[] = 'User-Agent: '.$this->useragent;

		$access = FacebookHelper::curl($url,false,$headers);

		$response = json_decode($access['body'],true);

		if (array_key_exists('error', $response)) {

			return [
				'status' => false,
				'response' => $response['error']['message']
			];

		}else{

			$cursor = false;
			if (array_key_exists('paging', $response)) {
				$cursor = $response['paging']['next'];
			}

			return [
				'status' => true,
				'response' => $response,
				'cursor' => $cursor
			];
		}
	}

	public function Extract($response)
	{

		$jsondata = $response['response'];

		$extract = array();
		foreach ($jsondata['data'] as $post) {

			$extractid = explode('_', $post['id']);
			$userid = $extractid[0];
			$postid = $extractid[1];

			$extract[] = [
				'userid' => $userid,
				'postid' => $postid
			];
		}

		return $extract;
	}

}