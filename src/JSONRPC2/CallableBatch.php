<?php

namespace JSONRPC2;

use \JSONRPC2\Protocol\Batch;
use \JSONRPC2\RemoteObject;

class CallableBatch extends Batch {

	private $remoteObject;

	public function __construct(RemoteObject $remoteObject) {

		parent::__construct();

		$this->remoteObject = $remoteObject;
	}

	public function end() {

		$requests = parent::end();

		return $this->remoteObject->getTransport()->reply($requests);
	}
}
