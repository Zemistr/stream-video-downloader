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
		$page = $this->loader->load($url);

		$result = array(
			'title'     => null,
			'qualities' => array()
		);

		if (preg_match('~Stream\.Data\.Episode\((?<data>.+)\);~isU', $page, $data)) {
			$data = @json_decode($data['data'], true);

			/********************** TITLE **********************/
			$title_parts = array();

			if (!empty($data['show_name'])) {
				$title_parts[] = $data['show_name'];
			}

			if (!empty($data['episode_name'])) {
				$title_parts[] = $data['episode_name'];
			}

			$result['title'] = implode(' - ', $title_parts);
			/********************** TITLE **********************/

			/******************** QUALITIES ********************/
			if (!empty($data['instances'])) {
				foreach ($data['instances'] as $instance) {
					$instance =& $instance['instances'][0];

					if (!empty($instance['quality_label'])) {
						$result['qualities'][$instance['quality']] = array(
							'source'        => $instance['source'],
							'quality'       => $instance['quality'],
							'quality_label' => $instance['quality_label']
						);
					}
				}
			}
			/******************** QUALITIES ********************/
		}

		return $result;
	}
} 
