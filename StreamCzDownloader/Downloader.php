<?php
namespace StreamCzDownloader;

use StreamCzDownloader\Drivers\IDriver;
use StreamCzDownloader\Drivers\NewDriver;
use StreamCzDownloader\Drivers\OldDriver;
use StreamCzDownloader\Loaders\ILoader;

/**
 * Class Downloader
 *
 * @version 3.0.0
 */
class Downloader {
	private $detect_driver = false;
	/** @var IDriver */
	private $driver;
	/** @var ILoader */
	private $loader;

	protected function setHeaders($result = true) {
		header("Content-Type: application/json; charset=UTF-8");

		if (!$result) {
			header("HTTP/1.0 404 Not Found");
		}
	}

	public function __construct() {
		spl_autoload_register(array($this, 'autoload'));
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
			throw new \RuntimeException('You must set Loader!');
		}

		if (!$this->driver && $this->detect_driver) {
			if (strpos($url, 'old.stream.cz') !== false) {
				$this->driver = new OldDriver($this->loader);
			}
			else {
				$this->driver = new NewDriver($this->loader);
			}
		}

		if (!$this->driver) {
			throw new \RuntimeException('You must set Driver!');
		}

		return $this->driver->getData($url) + array(
			'title'     => null,
			'qualities' => array()
		);
	}

	public function send($url) {
		$data = $this->load($url);
		$found = !empty($data['title']);

		$this->setHeaders($found);

		if ($found) {
			echo json_encode($data);

			return $data;
		}

		exit();
	}

	public function setDriver(IDriver $driver) {
		$this->driver = $driver;
	}

	public function setLoader(ILoader $loader) {
		$this->loader = $loader;
	}
}