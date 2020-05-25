<?php

namespace JSONRPC2\Errors;

use \JSONRPC2\Errors\GenericError;
use \OutOfRangeException;

class ServerError extends GenericError {

	public function __construct($code, $data = null) {

		if (!(self::MIN_CODE_SERVER_ERROR <= $code && $code <= self::MAX_CODE_SERVER_ERROR)) {
			throw new OutOfRangeException("Code ${code} is out of range of defined codes for this type of error.");
		}

		parent::__construct($code, 'Server error', $data);
	}
}
