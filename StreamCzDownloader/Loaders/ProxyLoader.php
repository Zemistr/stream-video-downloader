<?php
namespace StreamCzDownloader\Loaders;

class ProxyLoader implements ILoader {
	private $loops = 5;

	protected function getProxy($url) {
		$services = array(
			function ($url) {
				return 'http://go-connects.appspot.com/' . preg_replace('~https?://~', '', $url);
			},
			function ($url) {
				return 'http://www.proxyoption.info/index.php?q=' . urlencode($url) . '&hl=c0';
			},
			function ($url) {
				return 'http://www.anonyxy.info/index.php?q=' . urlencode($url) . '&hl=c0';
			},
			function ($url) {
				return 'http://www.melpy.info/index.php?q=' . urlencode($url) . '&hl=c0';
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
			$page = file_get_contents($this->getProxy($url), 0, $context);

			if ($page) {
				return $page;
			}

			$loops -= 1;
		}
		while ($loops);

		return false;
	}
} 