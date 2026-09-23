<?php

    interface MessageInterface {
        public function getHeaders(): array;
        public function getHeader(string $header): string;
        public function hasHeader(string $header): bool;
        public function getBody(): string;
    }

    interface RequestInterface extends MessageInterface {
        public function getMethod(): string;
        public function getUri(): string;
    }

    interface ResponseInterface extends MessageInterface {
        public function getStatusCode(): int;
    }

    class Request implements RequestInterface {
        private string $method;
        private string $uri;
        private array $headers;
        private string $body;

        public function getMethod(): string {
            return $this->method;
        }
        public function getUri(): string {
            return $this->uri;
        }
        public function getHeaders(): array {
            return $this->headers;
        }
        public function getHeader(string $header): string {
            return $this->headers[$header];
        }
        public function hasHeader(string $header): bool {
            return array_key_exists($header, $this->headers);
        }
        public function getBody(): string {
            return $this->body;
        }

    };