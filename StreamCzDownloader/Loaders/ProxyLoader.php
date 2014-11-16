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
			function ($url) {
				$url = 'https://proxy-nl.hide.me/go.php?u=' . urlencode($url) . '&b=0&f=norefer';
				$this->logger->log(__CLASS__ . '::getProxy() : ' . $url);

				return $url;
			},
			function ($url) {
				$url = 'https://proxy-us.hide.me/go.php?u=' . urlencode($url) . '&b=0&f=norefer';
				$this->logger->log(__CLASS__ . '::getProxy() : ' . $url);

				return $url;
			},
			function ($url) {
				$url = 'https://proxy-de.hide.me/go.php?u=' . urlencode($url) . '&b=0&f=norefer';
				$this->logger->log(__CLASS__ . '::getProxy() : ' . $url);

				return $url;
			},
			function ($url) {
				$url = 'https://www.filterbypass.me/anonsurf.php?u=' . urlencode($url) . '&b=0&f=norefer';
				$this->logger->log(__CLASS__ . '::getProxy() : ' . $url);

				return $url;
			},
			function ($url) {
				$url = 'http://onlineproxyfree.com/index.php?p=' . urlencode($url) . '&hl=c0';
				$this->logger->log(__CLASS__ . '::getProxy() : ' . $url);

				return $url;
			},
			function ($url) {
				$url = parse_url($url);
				$url['host'] .= '.prx.websiteproxy.co.uk';

				$url = $this->unparseUrl($url);
				$this->logger->log(__CLASS__ . '::getProxy() : ' . $url);

				return $url;
			},
			function ($url) {
				$url = 'http://www.zendproxy.com/bb.php?u=' . urlencode($url) . '&b=0&f=norefer';
				$this->logger->log(__CLASS__ . '::getProxy() : ' . $url);

				return $url;
			},
			function ($url) {
				$url = 'https://se.proxy.sumrando.com/wproxy/browse.php?u=' . urlencode($url) . '&b=0&f=norefer';
				$this->logger->log(__CLASS__ . '::getProxy() : ' . $url);

				return $url;
			},
			function ($url) {
				$url = 'http://boomproxy.com/browse.php?u=' . urlencode($url) . '&b=0&f=norefer';
				$this->logger->log(__CLASS__ . '::getProxy() : ' . $url);

				return $url;
			},
			function ($url) {
				$url = 'http://www.webproxy.net/view?q=' . urlencode($url);
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

	protected function unparseUrl(array $parsed_url) {
		$scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
		$host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
		$port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
		$user = isset($parsed_url['user']) ? $parsed_url['user'] : '';
		$pass = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
		$pass = ($user || $pass) ? "$pass@" : '';
		$path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
		$query = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
		$fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';

		return "$scheme$user$pass$host$port$path$query$fragment";
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
