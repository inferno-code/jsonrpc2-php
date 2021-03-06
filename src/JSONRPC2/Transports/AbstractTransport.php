<?php

namespace JSONRPC2\Transports;

use \JSONRPC2\Protocol\Request;
use \JSONRPC2\Protocol\Response;
use \JSONRPC2\Errors\GenericError;
use \InvalidArgumentException;
use \RuntimeException;

use function \json_encode;
use function \json_decode;
use function \is_array;
use function \is_object;
use function \is_numeric;
use function \is_string;
use function \is_int;
use function \array_map;
use function \property_exists;

abstract class AbstractTransport {

	public function __construct() { }

	protected function encodeRequest($request): string {

		return json_encode($request);
	}

	protected function decodeResponse(string $encodedResponse) {

		$response = json_decode($encodedResponse);

		if (is_array($response)) {
			return array_map(function ($item) { return $this->validateResponse($item); }, $response);
		}

		return $this->validateResponse($response);
	}

	private function validateResponse($response) {

		if (!is_object($response)) {
			throw new InvalidArgumentException('Invalid response. Object expected.');
		}

		if (!isset($response->jsonrpc) || $response->jsonrpc !== Response::VERSION) {
			throw new InvalidArgumentException('Invalid response. Unsupported version of protocol.');
		}

		if (isset($response->id)) {
			if ($response->id !== null && !is_numeric($response->id) && !is_string($response->id)) {
				throw new InvalidArgumentException('Invalid response. Unexpected type of identifier.');
			}
		}

		if (!property_exists($response, 'error') && !property_exists($response, 'result')) {
			throw new InvalidArgumentException('Invalid response. Controversial properties.');
		}

		if (isset($response->error)) {

			if (!is_object($response->error)
			|| !isset($response->error->code) || !is_int($response->error->code)
			|| !isset($response->error->message) || !is_string($response->error->message)) {
				throw new InvalidArgumentException('Invalid response. Invalid format of error desriptor.');
			}
			
			return new Response(
				new GenericError(
					$response->error->code,
					$response->error->message,
					$response->error->data ?? null
				),
				$response->id ?? null
			);
		}

		if (!property_exists($response, 'result')) {
			throw new InvalidArgumentException('Invalid response. Result expected.');
		}

		return new Response($response->result, $response->id ?? null);
	}

	private function isResponseIsRequired($request) {

		$required = false;

		if (is_array($request)) {
			foreach ($request as $request_) {
				if ($request_->isCall()) {
					$required = true;
					break;
				}
			}
		} else if ($request->isCall()) {
			$required = true;
		}

		return $required;
	}

	private function verifyMatching($request, $response) {

		if (is_array($request)) {

			if ($response === null) {
				$response = [ ];
			}

			if (!is_array($response)) {
				throw new RuntimeException('Unexpected response. Array expected, but single response was received.');
			}

			foreach ($request as $request_) {
				if (isset($request_->id) && $request_->id !== null) {
					$found = false;
					foreach ($response as $response_) {
						if (isset($response_->id) && $request_->id === $response_->id) {
							$found = true;
							break;
						}
					}
					if (!$found) {
						throw new RuntimeException('Array of responses does not contains all IDs of request.');
					}
				}
			}

			foreach ($response as $response_) {
				if (isset($response_->id) && $response_->id !== null) {
					$found = false;
					foreach ($request as $request_) {
						if (isset($request_->id) && $request_->id === $response_->id) {
							$found = true;
							break;
						}
					}
					if (!$found) {
						throw new RuntimeException('Unexcepted ID of response.');
					}
				}
			}

		} else {

			if ($request->isCall()) {

				if (is_array($response)) {
					throw new RuntimeException('Unexpected response. Single response expected, but array of responses was received.');
				}

				if ($response === null) {
					throw new RuntimeException('Response expected. Request represents a call, not notification.');
				}

				if (!isset($response->id) || $request->id !== $response->id) {
					throw new RuntimeException('ID of response does not match with ID of request.');
				}
			}
		}
	}

	public function reply($request) {

		$stringRequest = $this->encodeRequest($request);
		$stringResponse = $this->getResponse($stringRequest);
		$response = null;
		if ($this->isResponseIsRequired($request)) {
			$response = $this->decodeResponse($stringResponse);
		}

		$this->verifyMatching($request, $response);

		return $response;
	}

	abstract protected function getResponse(string $stringRequest): string;

}
