<?php
/**
 * Stream.cz downloader
 *
 * @version 3.0.0
 * @author  Martin Zeman (Zemistr)
 */

$url =& $_POST['url'];

if ($url === null) {
	$url =& $_GET['test_url'];
}

@file_get_contents('http://data.zemistr.eu/counter:stream/num-1-' . time() . '.png');

require('StreamCzDownloader/Downloader.php');

$downloader = new \StreamCzDownloader\Downloader();
$downloader->detectDriver();
$downloader->setLoader(new StreamCzDownloader\Loaders\ProxyLoader());
$data = $downloader->send($url);

@file_get_contents('http://s01.zemistr.eu/piwik/piwik.php?idsite=3&rec=1&url=' . $url . '&action_name=' . rawurlencode($data['title']) . '&cip=' . @$_SERVER['REMOTE_ADDR'] . '&_cvar={"1":["from","web"]}&token_auth=189b9ac0cf4f973d483038481cd0042b');
