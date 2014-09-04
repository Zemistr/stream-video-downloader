<?php
namespace StreamCzDownloader\Loaders;

use StreamCzDownloader\Loggers\ILogger;

class ProxyLoader implements ILoader {
	/** @var ILogger */
	private $logger;
	private $loops = 5;

	public function __construct(ILogger $logger) {
		$this->logger = $logger;
	}

	protected function getProxy($url) {
		$services = array(
			function ($url) {
				$url = 'http://go-connects.appspot.com/' . preg_replace('~https?://~', '', $url);
				$this->logger->log(__CLASS__ . '::getProxy() : ' . $url);

				return $url;
			},
			function ($url) {
				$url = 'http://www.proxyoption.info/index.php?q=' . urlencode($url) . '&hl=c0';
				$this->logger->log(__CLASS__ . '::getProxy() : ' . $url);

				return $url;
			},
			function ($url) {
				$url = 'http://www.anonyxy.info/index.php?q=' . urlencode($url) . '&hl=c0';
				$this->logger->log(__CLASS__ . '::getProxy() : ' . $url);

				return $url;
			},
			function ($url) {
				$url = 'http://www.melpy.info/index.php?q=' . urlencode($url) . '&hl=c0';
				$this->logger->log(__CLASS__ . '::getProxy() : ' . $url);

				return $url;
			},
			function ($url) {
				$url = 'http://anonymouse.cz/anonymizer/index.php?q=' . urlencode($url) . '&hl=c0';
				$this->logger->log(__CLASS__ . '::getProxy() : ' . $url);

				return $url;
			},
			function ($url) {
				$url = 'http://www.ipv6proxy.net/go.php?u=' . urlencode($url) . '&b=0&f=norefer';
				$this->logger->log(__CLASS__ . '::getProxy() : ' . $url);

				return $url;
			},
			function ($url) {
				$url = 'http://anonymouse.org/cgi-bin/anon-www.cgi/' . urlencode($url);
				$this->logger->log(__CLASS__ . '::getProxy() : ' . $url);

				return $url;
			},
			/*
			function ($url) {
				return $url;
			}
			*/
		);

		$service = $services[array_rand($services)];

		return $service($url);
	}

	public function load($url) {
		$this->logger->log(__METHOD__ . ': ' . $url);

		$opts = array(
			'http' => array(
				'method' => 'GET',
				// 'proxy'  => 'tcp://91.212.124.153:80',
				'header' => implode(
					PHP_EOL, array(
						'Accept: text/*',
						'User-Agent: Mozilla/5.0 Gecko/20100101 Firefox/18.0'
					)
				)
			)
		);
		$context = stream_context_create($opts);

		$loops = $this->loops;

		do {
			$page = @file_get_contents($this->getProxy($url), 0, $context);

			if ($page) {
				return $page;
			}

			$loops -= 1;
		} while ($loops);

		return false;
	}
}
