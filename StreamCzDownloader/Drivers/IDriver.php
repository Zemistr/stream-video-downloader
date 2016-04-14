<?php
namespace StreamCzDownloader\Drivers;

use StreamCzDownloader\Loaders\ILoader;
use StreamCzDownloader\Loggers\ILogger;

interface IDriver {
  public function __construct(ILoader $loader, ILogger $logger);

  public function getData($url);
}
