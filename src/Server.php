<?php

namespace App\WebServer;


class Server
{
    protected $host   = null;
    protected $port   = null;
    protected $socket = null;

    /**
     * Server constructor.
     * @param null $host
     * @param null $port
     *
     * @throws \Exception
     */
    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = (int) $port;

        $this->createSocket();
        $this->bind();
    }

    protected function createSocket(): void
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, 0);
    }

    /**
     * @throws \Exception
     */
    protected function bind(): void
    {
        if (!socket_bind($this->socket, $this->host, $this->port)) {
            throw new \Exception('Could not bind: '.$this->host.':'.$this->port.' - '.socket_strerror(socket_last_error()));
        }
    }

    /**
     * @param callable $callback
     */
    public function listen(callable $callback): void
    {
        while (true) {
            socket_listen($this->socket);

            if (false === $client = socket_accept($this->socket)) {
                socket_close( $client ); continue;
            }

            // In the real world of course you cannot just fix the max size to 1024..
            $request = Request::withHeaderString(socket_read( $client, 1024) );

            $response = call_user_func( $callback, $request );
            if ( !$response || !$response instanceof Response ) {
                $response = Response::error(404);
            }

            $response = (string) $response;
            socket_write( $client, $response, \strlen( $response ) );
            socket_close( $client );
        }
    }
}