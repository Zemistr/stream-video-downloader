<?php
namespace StreamCzDownloader\Loaders;

use StreamCzDownloader\Loggers\ILogger;

class ProxyLoader implements ILoader {
  /** @var ILogger */
  private $logger;
  private $loops = 10;
  /** @var \InfiniteIterator */
  private $proxies;
  private $temp_file;

  public function __construct(ILogger $logger) {
    $this->logger = $logger;
  }

  protected function getProxy() {
    if (!$this->proxies) {
      $iterator = new \ArrayIterator($this->loadProxies());
      $this->proxies = new \InfiniteIterator($iterator);
    }

    $this->proxies->rewind();
    $current = $this->proxies->current();

    return $current;
  }

  protected function loadProxies() {
    if (!is_file($this->temp_file)) {
      $headers = [
        'User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64; rv:36.0) Gecko/20100101 Firefox/36.0',
        "Host: free-proxy.cz",
      ];

      $opts = [
        'http' => [
          'header' => implode(PHP_EOL, $headers)
        ]
      ];

      $context = stream_context_create($opts);
      $page = file_get_contents('http://free-proxy.cz/cs/proxylist/country/all/https/ping/level3/1', 0, $context);

      $doc = new \DOMDocument();
      $doc->validateOnParse = true;
      @$doc->loadHTML($page);

      $xpath = new \DOMXPath($doc);
      $xpath_result = $xpath->query("//*[@id='proxy_list']/*/tr[*]/td[1]|//*[@id='proxy_list']/*/tr[*]/td[2]");
      $xpath_array = array_map(
        function (\DOMNode $node) {
          return trim($node->nodeValue);
        },
        iterator_to_array($xpath_result)
      );

      $iterator = new \ArrayIterator($xpath_array);
      $pairs = [];

      foreach ($iterator as $item) {
        if (is_numeric(str_replace('.', '', $item))) {
          $iterator->next();
          $pair = implode(':', [$item, $iterator->current()]);
          $pairs[$pair] = $pair;
        }
      }

      file_put_contents($this->temp_file, "<?php\nreturn " . var_export(array_values($pairs), true) . ";");
    }

    return require($this->temp_file);
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

  public function load($url, $original_url) {
    $this->logger->log(__METHOD__ . ': ' . $url);

    $headers = [
      'User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64; rv:36.0) Gecko/20100101 Firefox/36.0',
      'Accept: application/json, text/plain, */*',
      "Accept-Language: cs,en;q=0.5",
      "DNT: 1",
      "Host: www.stream.cz",
      "x-insight: activate",
      "Referer: $original_url"
    ];

    if (strpos($url, 'API')) {
      $data = ["95de526253c14", "fb5f58a", "820353bd70", "fd"];
      $time = microtime(true);

      $api_key = implode('', [$data[1], $data[2], $data[0], $data[3]]);
      $parts = explode('API', $url);

      $hash = implode('', [$api_key, array_pop($parts), round($time / 24 / 3600)]);
      $md5 = md5($hash);

      $headers[] = "Api-Password: $md5";
    }

    $opts = [
      'http' => [
        'method'  => 'GET',
        'timeout' => 5,
        'header'  => implode(PHP_EOL, $headers)
      ]
    ];

    $loops = $this->loops;

    do {
      $opts['http']['proxy'] = 'tcp://' . $this->getProxy();

      $context = stream_context_create($opts);
      $page = trim(file_get_contents($url, 0, $context));

      if ($page) {
        return $page;
      }

      $loops -= 1;
    } while ($loops);

    return false;
  }

  public function setTempDir($temp_dir) {
    $this->temp_file = "$temp_dir/proxies.php";
  }
}
