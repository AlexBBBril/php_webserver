<?php

namespace App\WebServer;


class Server
{
    protected $host = null;
    protected $port = null;
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


    protected function createSocket()
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, 0);
    }

    /**
     * @throws \Exception
     */
    protected function bind()
    {
        if (!socket_bind($this->socket, $this->host, $this->port)) {
            throw new \Exception('Could not bind: '.$this->host.':'.$this->port.' - '.socket_strerror(socket_last_error()));
        }
    }
}