<?php namespace Choi\TheTvDbApi;

class Mirror {

	private $id;
	private $mirrorpath;
	private $typemask;

	public function __construct($id, $mirrorpath, $typemask)
	{
		$this->id         = $id;
		$this->mirrorpath = $mirrorpath;
		$this->typemask   = $typemask;
	}

	public function __get($key)
	{
		return (isset($this->{$key})) ? $this->{$key} : null;
	}

}
