<?php

class Forbidden extends RuntimeException
{
  public function __construct(string $msg = "")
  {
    parent::__construct($msg, FORBIDDEN);
  }
}