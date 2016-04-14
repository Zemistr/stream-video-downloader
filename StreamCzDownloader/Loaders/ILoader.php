<?php
namespace StreamCzDownloader\Loaders;

use StreamCzDownloader\Loggers\ILogger;

interface ILoader {
  public function __construct(ILogger $logger);

  public function load($url, $original_url);
}
