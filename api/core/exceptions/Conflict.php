<?php

class Conflict extends Exception
{
  public function __construct(string $msg)
  {
    parent::__construct($msg, CONFLICT);
  }
}