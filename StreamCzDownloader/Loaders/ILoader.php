<?php
namespace StreamCzDownloader\Loaders;

interface ILoader {
	public function load($url);
}