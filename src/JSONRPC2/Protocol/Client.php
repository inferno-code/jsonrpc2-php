<?php

namespace JSONRPC2\Protocol;

use \JSONRPC2\Protocol\Request;
use \JSONRPC2\Protocol\Batch;

class Client {

	public function __construct() { }

	public function call($method, $params = null) {
		return new Request($method, $params, Request::getNextID());
	}

	public function notify($method, $params = null) {
		return new Request($method, $params);
	}

	public function batch(): Batch {
		return new Batch();
	}
}
