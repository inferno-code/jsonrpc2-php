<?php

namespace JSONRPC2\Errors;

use \JSONRPC2\Errors\GenericError;

class ParseError extends GenericError {

	public function __construct($data = null) {
		parent::__construct(self::CODE_PARSE_ERROR, 'Parse error', $data);
	}
}
