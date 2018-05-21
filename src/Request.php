<?php

namespace App\WebServer;


class Request
{
    protected $method     = null;
    protected $uri        = null;
    protected $parameters = [];
    protected $headers    = [];

    /**
     * Request constructor.
     * @param null $method
     * @param null $uri
     * @param array $headers
     */
    public function __construct($method, $uri, array $headers = [])
    {
        $this->headers = $headers;
        $this->method  = strtoupper($method);

        @list( $this->uri, $params ) = explode( '?', $uri );
        parse_str( $params, $this->parameters );
    }


    public static function withHeaderString(string $header)
    {
        $lines = explode("\n", $header);
        [$method, $uri] = explode(' ', array_shift($lines));

        $headers = [];

        foreach($lines as $line) {
            $line = trim($line);
            if (strpos( $line, ': ' ) !== false) {
                [$key, $value] = explode(': ', $line);
                $headers[$key] = $value;
            }
        }

        return new static($method, $uri, $headers);
    }
}