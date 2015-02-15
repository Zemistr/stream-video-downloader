<?php
require('StreamCzDownloader/Downloader.php');

$url = 'http://www.stream.cz/peklonataliri/10000721-ryze';

$downloader = new \StreamCzDownloader\Downloader();
$downloader->detectDriver();

$logger = new StreamCzDownloader\Loggers\MemoryLogger();
$loader = new StreamCzDownloader\Loaders\DirectLoader($logger);

$downloader->setLogger($logger);
$downloader->setLoader($loader);

if (isset($_GET['update'])) {
	file_put_contents('status_checker_test_data.json', json_encode($downloader->load($url)));
	exit('ok');
}

try {
	$live = json_encode($downloader->load($url));
}
catch (RuntimeException $exception) {
	$live = null;
}

$temp = @file_get_contents('status_checker_test_data.json');

$message = array(
	'status'   => 1,
	'messages' => array()
);

if ($live != $temp) {
	$message['status'] = 0;
	$message['messages'][] = 'Data are not identical.';
	$message['messages'][] = $url . '  X  http://stream.zemistr.eu/status_checker_test_data.json';
	$message['messages'][] = '------';
	$message['messages'][] = 'LOG:';

	$message['messages'] = array_merge($message['messages'], $logger->getLog());
}

//ob_clean();
//@header("Content-type: application/json; charset=UTF-8");
echo json_encode($message);
