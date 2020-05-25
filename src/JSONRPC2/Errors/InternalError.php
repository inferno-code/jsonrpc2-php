<?php

namespace JSONRPC2\Errors;

use \JSONRPC2\Errors\GenericError;

class InternalError extends GenericError {

	public function __construct($data = null) {
		parent::__construct(self::CODE_INTERNAL_ERROR, 'Internal error', $data);
	}
}
