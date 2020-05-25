<?php

namespace JSONRPC2\Transports;

use \JSONRPC2\Transports\AbstractTransport;
use \stdClass;
use \RuntimeException;
use \InvalidArgumentException;

use function \curl_init;
use function \curl_setopt_array;
use function \curl_exec;
use function \curl_error;
use function \sprintf;

class HTTP extends AbstractTransport {

	private $endpoint;
	private $options;

	public function __construct(string $endpoint, object $options = null) {

		parent::__construct();

		$this->setEndpoint($endpoint);
		$this->setOptions($options);
	}

	public function setEndpoint(string $endpoint) {

		$this->endpoint = $endpoint;
	}

	public function setOptions(object $options = null) {

		if ($options === null) {
			$options = new stdClass();
		}

		if (!isset($options->headers)) {

			$options->headers = [
				'Content-Type' => 'application/json'
			];

		} else if (!is_array($options->headers)) {

			throw new InvalidArgumentException('Unexpected headers. Array required.');
		}

		$options->method = 'POST';

		$this->options = $options;
	}

	protected function getHeaders(): array {

		$result = [];
		foreach ($this->options->headers as $name => $value) {
			$result[] = "{$name}: $value";
		}
		return $result;
	}

	protected function getResponse(string $request): string {

		$ch = curl_init($this->endpoint);
		curl_setopt_array(
			$ch,
			[
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HTTPHEADER => $this->getHeaders(),
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $request
			]
		);

		$response = curl_exec($ch);
		if ($response === false) {
			throw new RuntimeException(sprintf(
				'Server is unavailable: %s',
				curl_error($ch)
			));
		}

		return $response;
	}
}
