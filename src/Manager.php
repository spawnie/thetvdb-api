<?php namespace Choi\TheTvDbApi;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class Manager {

	/**
	 * Config object
	 *
	 * @var Choi\TheTvDbApi\Config
	 */
	private $config;

	/**
	 * Constructor
	 *
	 * @param  array $config
	 * @return void
	 */
	public function __construct(Config $config)
	{
		$this->config = $config;
	}

	/**
	 * Search series by name
	 *
	 * @param  string $name
	 * @return Illuminate\Support\Collection
	 */
	public function searchSeries($name)
	{
		$client   = new Client;
		$response = $client->get(sprintf('%s/api/GetSeries.php', $this->config->mirror), [
			'query' => [
				'seriesname' => $name,
				'language'   => $this->config->language,
			],
		]);
		$body = $response->xml();

		$results = [];
		foreach($body->Series as $entry)
		{
			$results[] = (array) $entry;
		}

		return new Collection($results);
	}

	/**
	 * Get series by ID
	 *
	 * @param  int $id
	 * @return Choi\TheTvDbApi\Series
	 */
	public function getSeriesById($id)
	{
		return new Series($this->config, $id);
	}

}
