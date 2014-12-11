<?php
namespace StreamCzDownloader\Drivers;

use StreamCzDownloader\Loaders\ILoader;
use StreamCzDownloader\Loggers\ILogger;

class NewDriver implements IDriver {
	/** @var ILoader */
	private $loader;

	/** @var ILogger */
	private $logger;

	public function __construct(ILoader $loader, ILogger $logger) {
		$this->loader = $loader;
		$this->logger = $logger;
	}

	public function getData($url) {
		$this->logger->log(__METHOD__ . ': ' . $url);

		$result = array(
			'title'     => null,
			'qualities' => array()
		);

		if (preg_match('~/(?<id>[0-9]{5,})\-~', $url, $matches)) {
			$url = "http://www.stream.cz/API/episode/$matches[id]";
		}

		$json = $this->loader->load($url);

		if ($json) {
			$data = @json_decode($json, true);

			/********************** TITLE **********************/
			$title_parts = array();

			if (!empty($data['_embedded']['stream:show']['name'])) {
				$title_parts[] = $data['_embedded']['stream:show']['name'];
			}

			if (!empty($data['name'])) {
				$title_parts[] = $data['name'];
			}

			$result['title'] = implode(' - ', $title_parts);
			/********************** TITLE **********************/

			/******************** QUALITIES ********************/
			if (!empty($data['video_qualities'])) {
				foreach ($data['video_qualities'] as $quality) {
					if (!empty($quality['quality_label'])) {
						$quality_format =& $quality['formats'][0];

						$result['qualities'][$quality['quality_label']] = array(
							'source'        => $quality_format['source'],
							'quality'       => $quality_format['quality'],
							'quality_label' => $quality['quality_label']
						);
					}
				}
			}
			/******************** QUALITIES ********************/
		}

		return $result;
	}
} 
