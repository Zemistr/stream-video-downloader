<?php
namespace StreamCzDownloader\Loggers;

class MemoryLogger implements ILogger {
  private $log = [];

  public function getLog() {
    return $this->log;
  }

  public function log($message) {
    $this->log[] = $message;
  }
}
