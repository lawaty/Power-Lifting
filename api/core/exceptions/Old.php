<?php

class Old extends Exception
{
  public function __construct(string $resource)
  {
    parent::__construct("$resource is old", FORBIDDEN);
  }
}