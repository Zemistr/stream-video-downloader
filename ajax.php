<?php
/**
 * @author  Martin Zeman (Zemistr) <me@zemistr.eu>
 * @version 4.0.0
 */
function load($url)
{
    $headers = [
      'User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64; rv:36.0) Gecko/20100101 Firefox/36.0',
      'Accept: application/json, text/plain, */*',
      'Accept-Language: cs,en;q=0.5',
      'DNT: 1',
      'Host: www.stream.cz',
      'x-insight: activate'
    ];

    if (strpos($url, 'API')) {
        $data = ['95de526253c14', 'fb5f58a', '820353bd70', 'fd'];
        $time = microtime(true);

        $apiKey = implode('', [$data[1], $data[2], $data[0], $data[3]]);
        $parts = explode('API', $url);

        $hash = implode('', [$apiKey, array_pop($parts), round($time / 24 / 3600)]);
        $md5 = md5($hash);

        $headers[] = "Api-Password: $md5";
    }

    $opts = [
      'http' => [
        'method' => 'GET',
        'header' => implode("\n", $headers)
      ]
    ];

    $context = stream_context_create($opts);
    $page = trim(file_get_contents($url, 0, $context));

    if ($page) {
        return $page;
    }

    return false;
}

function fetch($url)
{
    $result = [
      'title'     => null,
      'qualities' => []
    ];

    if (preg_match('~/(?<id>[0-9]{5,})\-~', $url, $matches)) {
        $url = "http://www.stream.cz/API/episode/$matches[id]"; // 10000721
    }

    $json = load($url);

    if ($json) {
        $data = @json_decode($json, true);

        /********************** TITLE **********************/
        $titleParts = [];

        if (!empty($data['_embedded']['stream:show']['name'])) {
            $titleParts[] = $data['_embedded']['stream:show']['name'];
        }

        if (!empty($data['name'])) {
            $titleParts[] = $data['name'];
        }

        $result['title'] = implode(' - ', $titleParts);
        /********************** TITLE **********************/

        /******************** QUALITIES ********************/
        if (!empty($data['video_qualities'])) {
            foreach ($data['video_qualities'] as $quality) {
                if (!empty($quality['formats'][0]['quality'])) {
                    $quality_format = $quality['formats'][0];

                    $result['qualities'][$quality_format['quality']] = [
                      'source'  => $quality_format['source'],
                      'quality' => $quality_format['quality']
                    ];
                }
            }
        }
        /******************** QUALITIES ********************/
    }

    return $result;
}

function send($url)
{
    $data = fetch($url) + ['title' => null, 'qualities' => []];

    header('Content-Type: application/json; charset=UTF-8');

    if (empty($data['title'])) {
        header('HTTP/1.0 404 Not Found');

        return;
    }

    echo json_encode($data);

    return $data;
}

if (!empty($_POST['url'])) {
    send($_POST['url']);
}
