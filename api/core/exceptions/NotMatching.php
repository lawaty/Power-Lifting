<?php

class NotMatching extends Exception
{
  public function __construct(string $resource1, string $resource2, int $code)
  {
    parent::__construct("$resource1 and $resource2 not matching", $code);
  }
}