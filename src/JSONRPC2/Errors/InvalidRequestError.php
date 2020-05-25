<?php

namespace JSONRPC2\Errors;

use \JSONRPC2\Errors\GenericError;

class InvalidRequestError extends GenericError {

	public function __construct($data = null) {
		parent::__construct(self::CODE_INVALID_REQUEST, 'Invalid Request', $data);
	}
}
