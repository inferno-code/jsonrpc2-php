<?php

namespace JSONRPC2\Protocol;

use \JSONRPC2\Protocol\Request;
use \InvalidArgumentException;

class Batch {

	private $requests;

	public function __construct(array $requests = []) {

		foreach ($requests as $request) {
			if (!($request instanceof Request)) {
				throw new InvalidArgumentException('Unexpected type of request.');
			}
		}

		$this->requests = $requests;
	}

	public function call($method, $params = null) {

		$this->requests[] = new Request($method, $params, Request::getNextID());
		return $this;
	}

	public function notify($method, $params = null) {

		$this->requests[] = new Request($method, $params);
		return $this;
	}

	public function end() {

		$batch = $this->requests;
		$this->requests = [];
		return $batch;
	}
}
