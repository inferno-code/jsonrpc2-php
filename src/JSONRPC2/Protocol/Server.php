<?php

namespace JSONRPC2\Protocol;

use \JSONRPC2\Protocol\Request;
use \JSONRPC2\Protocol\Response;
use \JSONRPC2\Errors\GenericError;
use \JSONRPC2\Errors\InternalError;
use \JSONRPC2\Errors\MethodNotFoundError;
use \Exception;

use function \is_array;
use function \array_map;
use function \array_diff_key;
use function \array_keys;
use function \call_user_func_array;

class Server {

	protected $methods = [];
	protected $parameters = [];

	public function __construct() {
	}

	public function on($method, array $parameters, callable $fn) {

		$this->methods[$method] = $fn;
		$this->parameters[$method] = $parameters;
	}

	public function off($method) {

		unset($this->methods[$method]);
		unset($this->parameters[$method]);
	}

	public function reply($request) {

		$response = null;

		if (is_array($request)) {
			$response = $this->processBatch($request);
		} else {
			$response = $this->processRequest($request);
		}

		return $response;
	}

	private function processRequest($request): ?Response {

		$id = $request->id ?? null;
		$method = $request->method;
		$parameters = $request->params ?? [];

		try {

			$result = $this->evaluate($method, $parameters);
			if ($id !== null) { // request is call
				return new Response($result, $id);
			}

		} catch (Exception $e) {

			if ($id !== null) { // request is call
				$error = ($e instanceof GenericError ? $e : new InternalError($e));
				return new Response($error, $id);
			}
		}

		// request is notification
		return null;
	}

	private function processBatch(array $requests): array {

		return array_map(function ($request) { return $this->processRequest($request); }, $requests);
	}

	private function evaluate($method, $parameters) {

		if (!isset($this->methods[$method]) || !isset($this->parameters[$method])) {
			throw new MethodNotFoundError();
		}

		$args = [];

		if (is_array($parameters)) {

			$args = $parameters;

		} else if (is_object($parameters)) {

			foreach ($this->parameters[$method] as $name) {
				$args[] = $parameters->$name;
			}
		}

		return call_user_func_array($this->methods[$method], $args);
	}
}
