<?php
namespace StreamCzDownloader\Drivers;

use StreamCzDownloader\Loaders\ILoader;

interface IDriver {
	public function __construct(ILoader $loader);

	public function getData($url);
}