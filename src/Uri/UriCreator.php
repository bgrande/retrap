<?php

declare(strict_types = 1);

class UriCreator {
    private array $server;

    public function __construct(array $server) {
        $this->server = $server;
    }

    public function createUri(string $filePath): string {
        $uri = 'http';

        if ((isset($this->server['HTTPS']) && $this->server['HTTPS'] === 'on') || $this->server['REQUEST_URI']) {
            $uri .= 's';
        }

        $uri .= '://' . $this->server['HTTP_HOST'];

        if ($this->server['REQUEST_URI'] !== $this->server['SCRIPT_NAME']) {
           $uri .= $this->server['REQUEST_URI'];
        }

        $uri = str_replace($this->server['SCRIPT_NAME'], '', $uri);
        $uri .= $filePath;

        return $uri;
    }
}
