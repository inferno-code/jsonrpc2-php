jsonrpc2-php
============

A simple JSON-RPC 2.0 implementation

## Features
- Correct: fully compliant with the [JSON-RPC 2.0 specifications](http://www.jsonrpc.org/specification)
- Has no depeddencies from other libraries and packages.
- Simple JSON-RPC 2.0 client and server code.

## Tasks to be completed
- Write examples
- Write some unit tests
- Write API docs

## Examples

### Typical client

```php
$obj = new \JSONRPC2\RemoteProxyObject(
    new \JSONRPC2\Transports\HTTP(
        'http://127.0.0.1:8001/some/endpoint'
    )
);

printf("result = %s\n", $obj->substract(50, 23));
```

### Typical server

```php
$server = new \JSONRPC2\ServerObject();

$server->on(
    'substract',
    [ 'minuend', 'subtrahend' ],
    function ($minuend, $subtrahend) {
        return $minuend - $subtrahend;
    }
);

$headers = [
    'Content-Type: application/json; charset=utf-8',
	'Access-Control-Allow-Origin: *',
	'Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS',
	'Access-Control-Allow-Headers: Authorization, Origin, Content-Type, Accept',
	'Access-Control-Max-Age: 5',
];

foreach ($headers as $header)
    header($header);

$encodedRequest = file_get_contents('php://input');
$encodedResponse = $server->reply($encodedRequest);

echo $encodedResponse;
```