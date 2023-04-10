<?php

class UniquenessViolated extends Exception
{
  public function __construct(string $conflict)
  {
    parent::__construct($conflict);
  }
}