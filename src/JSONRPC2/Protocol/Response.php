<?php

namespace JSONRPC2\Protocol;

use \JSONRPC2\Errors\GenericError;
use \stdClass;

class Response {

	const VERSION = '2.0';

	public function __construct($result, $id = null) {

		$this->jsonrpc = self::VERSION;

		if ($result instanceof GenericError) {

			$this->error = new stdClass();
			$this->error->code = $result->getCode();
			$this->error->message = $result->getMessage();

			$data = $result->getData();
			if ($data !== null) {
				$this->error->data = $data;
			}

		} else {

			$this->result = $result;
		}

		if ($id !== null) {
			$this->id = $id;
		}
	}

	public function hasError(): bool {

		return isset($this->error);
	}

	public function getError(): ?GenericError {

		if ($this->hasError()) {

			return new GenericError(
				$this->error->code,
				$this->error->message,
				$this->error->data ?? null
			);
		}
		
		return null;
	}
}
