<?php

/**
 * @property string $mapper_type
 */
abstract class Entity
{
  protected ?Mapper $mapper = null; // mapper instance
  protected bool $synced = true; // db synchronization flag

  protected array $ids = [
    'id' => null
  ];

  /**
   * copy properties from another entity
   */
  public function copy(Entity $entity): void
  {
    if (get_class($entity) != static::class)
      throw new IncompatibleEntities(static::class, get_class($entity));

    $fields = get_object_vars($this);
    foreach ($fields as $field => $value)
      $this->{$field} = $entity->naiveGet($field);
  }

  public function __construct($id)
  {
    $this->setID($id);
  }

  /**
   * Method used to load ALL required user properties and optional if found
   * @param array data to be loaded including required and optional info
   */
  public function load(array $data): void
  {
    foreach ($data as $property => $value)
      if (property_exists($this, $property))
        $this->set($property, $value);
  }

  /**
   * Function returns the entity id formatted in an associative array
   */
  public function getID(): array
  {
    return $this->ids;
  }

  /**
   * Method receives integer id or an associtative array of ids in case more than one id is required for this entity
   */
  public function setID($id): void
  {
    if (!is_array($id)) {
      $this->ids['id'] = $id;
      return;
    }

    foreach ($this->ids as $key => $value) {
      if (!key_exists($key, $id))
        throw new RequiredPropertyNotFound($key, static::class);

      if ($id[$key] <= 0)
        throw new InvalidID(static::class, $key);

      $this->ids[$key] = intval($id[$key]);
    }
  }

  // Setters and Getters
  public function naiveGet($property)
  {
    if (!property_exists($this, $property) && !isset($this->ids[$property]))
      throw new PropertyNotExisting($property, static::class);

    if (isset($this->ids[$property]))
      return $this->ids[$property];

    if (!isset($this->{$property}))
      return null;

    return $this->{$property};
  }

  public function get(...$properties)
  {
    $result = [];

    foreach ($properties as $property) {
      if (!$this->naiveGet($property) && !$this->inSync() && $this->get('id') > 0 && $entity = $this->getMapper()->get($this->getID()))
        $this->copy($entity);

      $result[$property] = $this->naiveGet($property);
    }

    if (count($result) == 1)
      return array_values($result)[0];

    return $result;
  }

  public function set(string $property, $value): void
  {
    if (isset($this->ids[$property]))
      return;

    if (!property_exists($this, $property))
      throw new PropertyNotExisting($property, static::class);

    if (is_float($value))
      $value = floatval($value);

    else if (is_numeric($value))
      $value = intval($value);

    else if (isJson($value))
      $value = json_decode($value, true);

    // Value has changed
    if (!isset($this->{$property}) || $this->{$property} != $value) {
      $this->{$property} = $value;

      // if persistence layer is concerned with the changed value
      if (in_array($property, $this->getMapper()::$required) || in_array($property, $this->getMapper()::$optional))
        $this->setSync(false);
    }
  }

  /**
   * Method returns a mapper for that instance
   */
  public function getMapper(): Mapper
  {
    if (!$this->mapper)
      $this->mapper = new static::$mapper_type($this);

    return $this->mapper;
  }

  //Sync with persistence layer functions
  public function inSync(): bool
  {
    /**
     * Method checks the object synchronization with database
     */
    return $this->synced;
  }

  public function setSync(bool $state): void
  {
    $this->synced = $state;
  }
}
