<?php
header("Content-Type: application/json; charset=UTF-8");

$url =& $_POST['url'];

$result = array(
	'title'     => null,
	'qualities' => array()
);

if(!empty($url)) {
	$page = @file_get_contents($url);

	if(preg_match('~Stream\.Data\.Episode\((?<data>[^\)]+)\)~isU', $page, $data)) {

		$data = @json_decode($data['data'], true);

		/********************** TITLE **********************/
		$title_parts = array();

		if(!empty($data['show_name'])) {
			$title_parts[] = $data['show_name'];
		}

		if(!empty($data['episode_name'])) {
			$title_parts[] = $data['episode_name'];
		}

		$result['title'] = implode(' - ', $title_parts);
		/********************** TITLE **********************/

		/******************** QUALITIES ********************/
		if(!empty($data['instances'])) {
			foreach($data['instances'] as $instance) {
				$instance =& $instance['instances'][0];

				if(!empty($instance['quality_label'])) {
					$result['qualities'][$instance['quality']] = array(
						'source'        => $instance['source'],
						'quality'       => $instance['quality'],
						'quality_label' => $instance['quality_label']
					);
				}
			}
		}
		/******************** QUALITIES ********************/
	}
}

@file_get_contents('http://data.zemistr.eu/counter:stream/num-1-' . time() . '.png');
@file_get_contents('http://s01.zemistr.eu/piwik/piwik.php?idsite=3&rec=1&url=' . $_GET['adresa'] . '&action_name=' . rawurlencode($title) . '&cip=' . @$_SERVER['REMOTE_ADDR'] . '&_cvar={"1":["from","web"]}&token_auth=189b9ac0cf4f973d483038481cd0042b');

echo json_encode($result);