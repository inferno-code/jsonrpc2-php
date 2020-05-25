<?php

namespace JSONRPC2;

use \JSONRPC2\Protocol\Client;
use \JSONRPC2\Transports\AbstractTransport;
use \JSONRPC2\CallableBatch;

class RemoteObject extends Client {

	private $transport;

	public function __construct(AbstractTransport $transport) {

		parent::__construct();

		$this->setTransport($transport);
	}

	public function getTransport(): AbstractTransport {

		return $this->transport;
	}

	public function setTransport(AbstractTransport $transport) {

		$this->transport = $transport;
	}

	public function call($method, $params = null) {

		$request = parent::call($method, $params);

		return $this->transport->reply($request);
	}

	public function notify($method, $params = null) {

		$request = parent::notify($method, $params);

		return $this->transport->reply($request);
	}

	public function batch(): CallableBatch {

		return new CallableBatch($this);
	}
}
