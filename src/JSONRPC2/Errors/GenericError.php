<?php

namespace JSONRPC2\Errors;

use \Exception;
use \InvalidArgumentException;

class GenericError extends Exception {

	const CODE_PARSE_ERROR = -32700;
	const CODE_INVALID_REQUEST = -32600;
	const CODE_METHOD_NOT_FOUND = -32601;
	const CODE_INVALID_PARAMETERS = -32602;
	const CODE_INTERNAL_ERROR = -32603;
	const MIN_CODE_SERVER_ERROR = -32099;
	const MAX_CODE_SERVER_ERROR = -32000;

	protected $data;

	public function __construct($code, $message, $data = null) {

		if (!$this->isValidCode($code)) {
			throw new InvalidArgumentException("Invalid error code ${code}.");
		}

		parent::__construct($message, $code);

		$this->data = $data;
	}

	protected function isValidCode($code) {

		if (self::MIN_CODE_SERVER_ERROR <= $code && $code <= self::MAX_CODE_SERVER_ERROR) {
			return true;
		}

		switch ($code) {
			case self::CODE_PARSE_ERROR:
			case self::CODE_INVALID_REQUEST:
			case self::CODE_METHOD_NOT_FOUND:
			case self::CODE_INVALID_PARAMETERS:
			case self::CODE_INTERNAL_ERROR:
				return true;
		}

		return false;
	}

	public function getData() {
		return $this->data;
	}
}
