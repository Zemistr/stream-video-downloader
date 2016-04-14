<?php
namespace StreamCzDownloader;

use StreamCzDownloader\Drivers\IDriver;
use StreamCzDownloader\Drivers\NewDriver;
use StreamCzDownloader\Drivers\OldDriver;
use StreamCzDownloader\Loaders\ILoader;
use StreamCzDownloader\Loggers\ILogger;

/**
 * Class Downloader
 *
 * @version 3.1.0
 */
class Downloader {
  private $detect_driver = false;

  /** @var IDriver */
  private $driver;

  /** @var ILoader */
  private $loader;

  /** @var ILogger */
  private $logger;

  public function __construct() {
    spl_autoload_register([$this, 'autoload']);
  }

  protected function setHeaders($result = true) {
    header('Content-Type: application/json; charset=UTF-8');

    if (!$result) {
      header('HTTP/1.0 404 Not Found');
    }
  }

  public function autoload($class_name) {
    $class_name = ltrim($class_name, '\\');
    $file_name = '';

    if ($last_namespace_pos = strrpos($class_name, '\\')) {
      $namespace = substr($class_name, 0, $last_namespace_pos);
      $class_name = substr($class_name, $last_namespace_pos + 1);
      $file_name = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $file_name .= str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php';

    require $file_name;
  }

  public function detectDriver($enable = true) {
    $this->detect_driver = (bool)$enable;
  }

  public function load($url) {
    if (!$this->loader) {
      $this->logger->log('You must set Loader!');
      throw new \RuntimeException('You must set Loader!');
    }

    if (!$this->driver && $this->detect_driver) {
      if (strpos($url, 'old.stream.cz') !== false) {
        $this->logger->log('Autodetect driver: OldDriver');
        $this->driver = new OldDriver($this->loader, $this->logger);
      }
      else {
        $this->logger->log('Autodetect driver: NewDriver');
        $this->driver = new NewDriver($this->loader, $this->logger);
      }
    }

    if (!$this->driver) {
      $this->logger->log('You must set Driver!');
      throw new \RuntimeException('You must set Driver!');
    }

    return $this->driver->getData($url) + [
      'title'     => null,
      'qualities' => []
    ];
  }

  public function send($url) {
    $data = $this->load($url);
    $found = !empty($data['title']);

    $this->setHeaders($found);

    if ($found) {
      echo json_encode($data);

      return $data;
    }
  }

  public function setDriver(IDriver $driver) {
    $this->driver = $driver;
  }

  public function setLogger(ILogger $logger) {
    $this->logger = $logger;
  }

  public function setLoader(ILoader $loader) {
    $this->loader = $loader;
  }
}
