<?php

namespace JSONRPC2\Protocol;

class Request {

	const VERSION = '2.0';

	public function __construct(string $method, $params = [], $id = null) {

		$this->jsonrpc = self::VERSION;
		$this->method = $method;
		$this->params = $params;

		if ($id !== null) {
			$this->id = $id;
		}
	}

	public function isCall(): bool {

		return isset($this->id) && $this->id !== null;
	}

	public function isNotification(): bool {

		return !$this->isCall();
	}

	public static function getNextID() {
		static $nextRequestID = 1;
		return $nextRequestID ++;
	}
}
