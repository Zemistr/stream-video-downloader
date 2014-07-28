<?php
/**
 * Stream.cz downloader
 *
 * @version 2.0.1
 * @author  Martin Zeman (Zemistr)
 */

header("Content-Type: application/json; charset=UTF-8");

$url =& $_POST['url'];
$cache = true;

if ($url === null) {
	$url =& $_GET['test_url'];
	$cache = false;
}

$result = array(
	'title'     => null,
	'qualities' => array()
);

function proxy($url) {
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

if (!empty($url)) {
	$sha1_url = sha1($url);
	$cache_file = 'cache/' . $sha1_url . '.php';

	if ($cache && is_file($cache_file) && filemtime($cache_file) > strtotime('-7 day')) {
		$result = require $cache_file;
		$result['source'] = 'cache';
	} else {
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

		$url = proxy($url);
		$context = stream_context_create($opts);
		$page = file_get_contents($url, 0, $context);

		if (!$page) {
			$url = proxy($url);
			$context = stream_context_create($opts);
			$page = file_get_contents($url, 0, $context);
		}

		if (preg_match('~Stream\.Data\.Episode\((?<data>.+)\);~isU', $page, $data)) {
			$data = @json_decode($data['data'], true);

			/********************** TITLE **********************/
			$title_parts = array();

			if (!empty($data['show_name'])) {
				$title_parts[] = $data['show_name'];
			}

			if (!empty($data['episode_name'])) {
				$title_parts[] = $data['episode_name'];
			}

			$result['title'] = implode(' - ', $title_parts);
			/********************** TITLE **********************/

			/******************** QUALITIES ********************/
			if (!empty($data['instances'])) {
				foreach ($data['instances'] as $instance) {
					$instance =& $instance['instances'][0];

					if (!empty($instance['quality_label'])) {
						$result['qualities'][$instance['quality']] = array(
							'source'        => $instance['source'],
							'quality'       => $instance['quality'],
							'quality_label' => $instance['quality_label']
						);
					}
				}

				file_put_contents('cache/' . $sha1_url . '.php', '<?php return ' . var_export($result, true) . ';');

				if ($cache) {
					$result['source'] = 'live';
				}
			}
			/******************** QUALITIES ********************/
		}
	}
}

@file_get_contents('http://data.zemistr.eu/counter:stream/num-1-' . time() . '.png');
@file_get_contents('http://s01.zemistr.eu/piwik/piwik.php?idsite=3&rec=1&url=' . $_GET['adresa'] . '&action_name=' . rawurlencode($result['title']) . '&cip=' . @$_SERVER['REMOTE_ADDR'] . '&_cvar={"1":["from","web"]}&token_auth=189b9ac0cf4f973d483038481cd0042b');

echo json_encode($result);