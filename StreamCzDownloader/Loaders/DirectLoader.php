<?php
namespace StreamCzDownloader\Loaders;

use StreamCzDownloader\Loggers\ILogger;

class DirectLoader implements ILoader {
	/** @var ILogger */
	private $logger;

	public function __construct(ILogger $logger) {
		$this->logger = $logger;
	}

	public function load($url, $original_url) {
		$this->logger->log(__METHOD__ . ': ' . $url);

		$headers = array(
			'User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64; rv:36.0) Gecko/20100101 Firefox/36.0',
			'Accept: application/json, text/plain, */*',
			"Accept-Language: cs,en;q=0.5",
			"DNT: 1",
			"Host: www.stream.cz",
			"x-insight: activate",
			"Referer: $original_url"
		);

		if (strpos($url, 'API')) {
			$data = array("95de526253c14", "fb5f58a", "820353bd70", "fd");
			$time = microtime(true);

			$api_key = implode('', array($data[1], $data[2], $data[0], $data[3]));
			$parts = explode('API', $url);

			$hash = implode('', array($api_key, array_pop($parts), round($time / 24 / 3600)));
			$md5 = md5($hash);

			$headers[] = "Api-Password: $md5";
		}

		$opts = array(
			'http' => array(
				'method' => 'GET',
				'header' => implode(PHP_EOL, $headers)
			)
		);

		$context = stream_context_create($opts);
		$page = trim(file_get_contents($url, 0, $context));

		if ($page) {
			return $page;
		}

		return false;
	}
}
