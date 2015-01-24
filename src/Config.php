<?php namespace Choi\TheTvDbApi;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class Config {

	/**
	 * Data array
	 *
	 * @var array
	 */
	private $data;

	/**
	 * Constructor
	 *
	 * @param  array $data
	 * @return void
	 */
	public function __construct(array $data)
	{
		// Store user config
		$this->data = $data;

		// Fetch mirrors
		$this->mirrors = $this->getMirrors();

		// Set first mirror as active mirror
		$this->setActiveMirror(0);
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

	/**
	 * Magic setter
	 *
	 * @param  string $key
	 * @param  mixed  $value
	 * @return void
	 */
	public function __set($key, $value)
	{
		$this->data[$key] = $value;
	}

	/**
	 * Available mirrors
	 *
	 * @param  void
	 * @return Illuminate\Support\Collection
	 */
	public function getMirrors()
	{
		$client   = new Client;
		$response = $client->get(sprintf('http://thetvdb.com/api/%s/mirrors.xml', $this->api_key));
		$body     = $response->xml();

		$mirrors = [];
		foreach($body->Mirror as $mirror)
		{
			$mirrors[] = new Mirror($mirror->id, $mirror->mirrorpath, $mirror->typemask);
		}

		return new Collection($mirrors);
	}

	/**
	 * Set active mirror
	 *
	 * @param  int $index
	 * @return $this
	 */
	public function setActiveMirror($index)
	{
		$this->mirror = $this->mirrors->get($index)->mirrorpath;
		return $this;
	}

}
