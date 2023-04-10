<?php

class NotModified extends Exception
{
  public function __construct(string $resource)
  {
    parent::__construct("$resource Not Modified", NOT_MODIFIED);
  }
}