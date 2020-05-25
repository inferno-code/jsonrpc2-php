<?php

namespace JSONRPC2\Errors;

use \JSONRPC2\Errors\GenericError;

class MethodNotFoundError extends GenericError {

	public function __construct($data = null) {
		parent::__construct(self::CODE_METHOD_NOT_FOUND, 'Method not found', $data);
	}
}
