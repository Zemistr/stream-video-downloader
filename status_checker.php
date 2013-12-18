<?php
$url = 'http://stream.zemistr.eu/stream_cz.php?test_url=' . urlencode('http://www.stream.cz/nejnovejsi/peklonataliri/10000721-ryze');
$live = file_get_contents($url);

if(isset($_GET['update'])) {
	file_put_contents('status_checker_test_data.json', $live);
}

$temp = file_get_contents('status_checker_test_data.json');

$message = array(
	'status'   => 1,
	'messages' => array()
);

if($live != $temp) {
	$message['status'] = 0;
	$message['messages'][] = 'Data are not identical.';
	$message['messages'][] = $url . '  X  http://stream.zemistr.eu/status_checker_test_data.json';
}

//ob_clean();
//@header("Content-type: application/json; charset=UTF-8");
echo json_encode($message);
