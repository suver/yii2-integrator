<?php
namespace suver\integrator;

class StorageIntegrator {

	protected $memStorage = [];

	public function set($cluster, $data, $storage = null)
	{
		if (!isset($this->memStorage[$cluster])) {
			$this->memStorage[$cluster] = [];
		}
		$this->memStorage[$cluster][$storage] = $data;
	}

	public function push($cluster, $data, $storage = null)
	{
		if (!isset($this->memStorage[$cluster])) {
			$this->memStorage[$cluster] = [];
		}
		$this->memStorage[$cluster][$storage][] = $data;
	}

	public function getCluster($cluster)
	{
		return empty($this->memStorage[$cluster]) ? null : $this->memStorage[$cluster];
	}

	public function has($cluster, $storage = null)
	{
		if (!isset($this->memStorage[$cluster])) {
			return false;
		}
		return $this->memStorage[$cluster][$storage] ? true : false;
	}

	public function get($cluster, $storage = null)
	{
		if (!isset($this->memStorage[$cluster])) {
			return null;
		}
		return $this->memStorage[$cluster][$storage] ?: null;
	}
}