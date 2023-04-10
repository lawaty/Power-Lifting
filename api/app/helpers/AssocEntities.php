<?php

class AssocEntities extends Entities
{
  protected array $keys = [];

  public function __construct(array $entities_array, string $entity_name)
  {
    parent::__construct($entities_array, $entity_name);
    $this->keys = array_keys($entities_array);
  }

  public function key(): int
  {
    return $this->keys[$this->index];
  }

  public function valid(): bool
  {
    return isset($this->keys[$this->index]);
  }
}
