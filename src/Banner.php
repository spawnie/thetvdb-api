<?php namespace Choi\TheTvDbApi;

use GuzzleHttp\Client;

class Banner {

	/**
	 * Config object
	 *
	 * @var Choi\TheTvDbApi\Config
	 */
	private $config;

	/**
	 * Series data array
	 *
	 * @var array
	 */
	private $data;

	/**
	 * Constructor
	 *
	 * @param  Choi\TheTvDbApi\Config $config
	 * @param  int $id
	 * @return void
	 */
	public function __construct(Config $config, array $data)
	{
		$this->config = $config;
		$this->data   = $data;
	}

	/**
	 * Magic getter
	 *
	 * @param  string $key
	 * @return mixed|null
	 */
	public function __get($key)
	{
		return (isset($this->data[$key])) ? $this->data[$key] : null;
	}

}
