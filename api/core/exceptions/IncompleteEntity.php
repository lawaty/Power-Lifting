<?php

class IncompleteEntity extends RuntimeException
{
  public function __construct($model_type)
  {
    parent::__construct("$model_type instance has no ID", FAIL);
  }
}