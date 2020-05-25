<?php

namespace JSONRPC2;

use \JSONRPC2\RemoteObject;
use \JSONRPC2\Transports\AbstractTransport;
use \RuntimeException;

class RemoteProxyObject {

	private $remoteObject;

	public function __construct(AbstractTransport $transport) {

		$this->remoteObject = new RemoteObject($transport);
	}

	public function __call(string $name, array $arguments) {

		$response =  $this->remoteObject->call($name, $arguments);

		if (is_array($response)) {
			throw new RuntimeException('Unexpected response type. Single response expected.');
		}

		if ($response->hasError()) {
			throw $response->getError();
		}

		return $response->result;
	}

	public function __get(string $name) {

		return $this->__call($name, []);
	}
}
