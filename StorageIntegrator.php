<?php
namespace suver\integrator;

class StorageIntegrator {

	protected $memStorage = [];

	public function set($cluster, $data, $storage = null)
	{
		$this->memStorage[$cluster][$storage] = $data;
	}

	public function push($cluster, $data, $storage = null)
	{
		$this->memStorage[$cluster][$storage][] = $data;
	}

	public function getCluster($cluster)
	{
		return empty($this->memStorage[$cluster]) ? null : $this->memStorage[$cluster];
	}

	public function has($cluster, $storage = null)
	{
		return $this->memStorage[$cluster][$storage] ? true : false;
	}

	public function get($cluster, $storage = null)
	{
		return $this->memStorage[$cluster][$storage] ?: null;
	}
}