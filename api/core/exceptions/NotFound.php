<?php

class NotFound extends Exception
{
  public function __construct(string $resource)
  {
    parent::__construct("$resource Not Found", NOT_FOUND);
  }
}