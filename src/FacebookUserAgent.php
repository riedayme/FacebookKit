<?php namespace Riedayme\FacebookKit;

class FacebookUserAgent
{

	const USERAGENT = [
	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36',
	'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36'
	];

	public static function Generate()
	{
		$randomIdx = array_rand(self::USERAGENT, 1);

		return self::USERAGENT[$randomIdx];
	}

	public static function GenerateStatic()
	{
		return "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36";
	}
}