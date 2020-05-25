<?php

namespace JSONRPC2;

use \JSONRPC2\Protocol\Server;
use \JSONRPC2\Protocol\Request;
use \JSONRPC2\Protocol\Response;
use \JSONRPC2\Errors\GenericError;
use \JSONRPC2\Errors\InvalidRequestError;
use \JSONRPC2\Errors\ParseError;
use \Exception;

use function \is_array;
use function \is_object;
use function \is_string;
use function \is_numeric;
use function \array_map;
use function \json_encode;
use function \json_decode;

class ServerObject extends Server {

	private function validateRequest($request) {

		if (!is_object($request)) {
			throw new InvalidRequestError('Request is not an object.');
		}

		if (!isset($request->jsonrpc) || $request->jsonrpc !== Request::VERSION) {
			throw new InvalidRequestError('Unsupported version of protocol.');
		}

		if (!isset($request->method) || !is_string($request->method)) {
			throw new InvalidRequestError('Method is not defined.');
		}

		if (isset($request->id)) {
			if ($request->id !== null && !is_numeric($request->id) && !is_string($request->id)) {
				throw new InvalidRequestError('Unexpected type of identifier.');
			}
		}

		if (isset($request->params) && !is_object($request->params) && !is_array($request->params)) {
			throw new InvalidRequestError('Unexpected parameters.');
		}

		return new Request($request->method, $request->params ?? null, $request->id ?? null);
	}

	public function reply($encodedRequest) {

		$response = null;

		$request = @json_decode($encodedRequest);

		if ($request === null && trim($encodedRequest) !== 'null') {

			$response = new Response(new ParseError());

		} else if (is_array($request)) {

			if (count($request) <= 0) {

				$response = new Response(new InvalidRequestError('An empty array was sent as request.'));

			} else {

				$response = [];

				foreach ($request as $request_) {

					try {

			   			$request_ = $this->validateRequest($request_);
						$response_ = parent::reply($request_);

					} catch (GenericError $genericError) {

						$response_ = new Response($genericError);
					}

					if ($response_ !== null) {
						$response[] = $response_;
					}
				}

				if (count($response) <= 0) {
					$response = null;
				}
			}

		} else {

			try {

	   			$request = $this->validateRequest($request);
				$response = parent::reply($request);

			} catch (GenericError $genericError) {

				$response = new Response($genericError);
			}

		}

		if ($response === null) {
			return ''; // notifications do not have a response object to be returned
		}

		return json_encode($response);
	}
}
