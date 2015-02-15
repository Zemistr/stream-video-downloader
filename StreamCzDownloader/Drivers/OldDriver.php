<?php
namespace StreamCzDownloader\Drivers;

use StreamCzDownloader\Loaders\ILoader;
use StreamCzDownloader\Loggers\ILogger;

class OldDriver implements IDriver {
	/** @var ILoader */
	private $loader;

	/** @var ILogger */
	private $logger;

	public function __construct(ILoader $loader, ILogger $logger) {
		$this->loader = $loader;
		$this->logger = $logger;
	}

	private function getUrl($id) {
		return 'http://cdn-dispatcher.stream.cz/?id=' . $id;
	}

	public function getData($url) {
		$this->logger->log(__METHOD__ . ': ' . $url);
		$page = $this->loader->load($url, $url);

		$result = array(
			'title'     => null,
			'qualities' => array()
		);

		if (preg_match('~<title>(?<title>.+)<\/title>~isU', $page, $title)) {
			$result['title'] = $title['title'];

			if (preg_match('~param name="flashvars" value="(?<value>.+)"~isU', $page, $flashvars_data)) {
				parse_str($flashvars_data['value'], $flashvars);

				if ((isset($flashvars['cdnLQ']) && $lq_id = $flashvars['cdnLQ']) || (isset($flashvars['cdnID']) && $lq_id = $flashvars['cdnID'])) {
					$result['qualities']['360p'] = array(
						'source'        => $this->getUrl($lq_id),
						'quality'       => '360p',
						'quality_label' => 'Nízká'
					);
				}

				if ((isset($flashvars['cdnHQ']) && $hq_id = $flashvars['cdnHQ']) || (isset($flashvars['hdID']) && $hq_id = $flashvars['hdID'])) {
					$result['qualities']['480p'] = array(
						'source'        => $this->getUrl($hq_id),
						'quality'       => '480p',
						'quality_label' => 'Střední'
					);
				}

				if ((isset($flashvars['cdnHD']) && $hd_id = $flashvars['cdnHD'])) {
					$result['qualities']['720p'] = array(
						'source'        => $this->getUrl($hd_id),
						'quality'       => '720p',
						'quality_label' => 'Vysoká'
					);
				}
			}
		}

		return $result;
	}
}
