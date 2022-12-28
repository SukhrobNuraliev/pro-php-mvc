<?php

namespace Framework\Http;

use InvalidArgumentException;

class Response
{
    const REDIRECT = 'REDIRECT';
    const HTML = 'HTML';
    const JSON = 'JSON';
    private string $type = 'HTML';
    private ?string $redirect = null;
    private mixed $content = '';
    private int $status = 200;
    private array $headers = [];

    public function content(mixed $content = null): mixed
    {
        if (is_null($content)) {
            return $this->content;
        }
        $this->content = $content;
        return $this;
    }

    public function status(int $code = null): int|static
    {
        if (is_null($code)) {
            return $this->code;
        }
        $this->code = $code;
        return $this;
    }

    public function header(string $key, string $value): static
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function redirect(string $redirect = null): mixed
    {
        if (is_null($redirect)) {
            return $this->redirect;
        }
        $this->redirect = $redirect;
        $this->type = static::REDIRECT;
        return $this;
    }

    public function json(mixed $content): static
    {
        $this->content = $content;
        $this->type = static::JSON;
        return $this;
    }

    public function type(string $type = null): string|static
    {
        if (is_null($type)) {
            return $this->type;
        }
        $this->type = $type;
        return $this;
    }

    public function send(): string
    {
        foreach ($this->headers as $key => $value) {
            header("{$key}: {$value}");
        }
        if ($this->type === static::HTML) {
            header('Content-Type: text/html');
            http_response_code($this->status);
            print $this->content;
        }
        if ($this->type === static::JSON) {
            header('Content-Type: application/json');
            http_response_code($this->status);
            print json_encode($this->content);
        }
        if ($this->type === static::REDIRECT) {
            header("Location: {$this->redirect}");
            http_response_code($this->code);
        }
        throw new InvalidArgumentException("{$this->type} is not a recognised type");
    }
}