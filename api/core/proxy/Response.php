<?php

const SUCCESS = 200;
const NO_CONTENT = 204;

const REDIRECT = 300;
const NOT_MODIFIED = 304;

const BAD_REQUEST = 400;
const UNAUTHORIZED = 401;
const FORBIDDEN = 403;
const NOT_FOUND = 404;
const CONFLICT = 409;
const FAIL = 500;

class Response
{
  private int $code;
  private $body;
  public string $hidden_buffer;

  public function __construct($code, $body = '')
  {
    $this->code = $code;
    $this->body = $body;
  }

  public function format()
  {
    return [
      'code' => $this->code,
      'body' => $this->body
    ];
  }

  public function getCode(): int
  {
    return $this->code;
  }

  public function setCode(int $code)
  {
    $this->code = $code;
  }

  public function getBody()
  {
    return $this->body;
  }

  public function setBody($code): void
  {
    $this->code = $code;
  }

  public function echo(): void
  {
    header('Content-type: text/plain', true);
    header('Connection: close');
    ignore_user_abort(true);
    $this->hidden_buffer = ob_get_contents();
    ob_clean();

    $body = '';
    if (is_iterable($this->body))
      $body = json_encode($this->body, JSON_UNESCAPED_UNICODE);
    else if (is_string($this->body))
      $body = $this->body;

    if ((!strlen($body) || $body == '[]') && $this->code >= 200 && $this->code < 300)
      $this->code = NO_CONTENT;

    echo $body;
    http_response_code($this->code);

    // Closing Connection
    header('Content-Length: ' . ob_get_length());
    while (ob_get_level() > 0)
      ob_end_flush();

    flush();
  }
}
