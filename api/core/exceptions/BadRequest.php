<?php

class BadRequest extends RuntimeException
{
  public function __construct(string $msg)
  {
    parent::__construct($msg, BAD_REQUEST);
  }
}