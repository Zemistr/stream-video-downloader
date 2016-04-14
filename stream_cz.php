<?php
/**
 * Stream.cz downloader
 *
 * @version 3.2.2
 * @author  Martin Zeman (Zemistr)
 */

$url =& $_POST['url'];

if ($url === null) {
  $url =& $_GET['test_url'];
}

require('StreamCzDownloader/Downloader.php');

$downloader = new \StreamCzDownloader\Downloader();
$downloader->detectDriver();

$logger = new StreamCzDownloader\Loggers\MemoryLogger();
$loader = new StreamCzDownloader\Loaders\DirectLoader($logger);

$downloader->setLogger($logger);
$downloader->setLoader($loader);
$data = $downloader->send($url);

@file_get_contents('http://piwik.zdft.net/piwik/piwik.php?idsite=7&rec=1&url=' . $url . '&action_name=' . rawurlencode($data['title']) . '&cip=' . @$_SERVER['REMOTE_ADDR'] . '&token_auth=13a8deb4bf6f0bbec63240301c0142c0');
