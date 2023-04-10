<?php

class NegativeSectionReached extends RuntimeException
{
  public function __construct(string $msg = "Negative Section Reached")
  {   
    parent::__construct($msg);
  }
}