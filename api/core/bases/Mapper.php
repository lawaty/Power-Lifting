<?php

/**
 * @property string $table
 * @property array $required
 * @property array $optional
 * @property string $entity_type
 */
abstract class Mapper
{
  protected Entity $entity; // entity instance
  public static array $required = [];
  public static array $optional = [];

  public static function getTableName(): string
  {
    return static::$table;
  }

  /**
   * Function makes the data ready for insertion
   */
  public static function sanitize(array $data): array
  {
    $healthy = [];
    foreach (static::$required as $property) {
      if (!isset($data[$property]))
        throw new RequiredPropertyNotFound($property, static::$entity_type);

      if (is_array($data[$property]))
        $healthy[$property] = json_encode($data[$property]);
      else
        $healthy[$property] = $data[$property];
    }

    foreach (static::$optional as $property) {
      if (!isset($data[$property]) || $data[$property] == null)
        continue;

      if (is_array($data[$property]))
        $healthy[$property] = json_encode($data[$property]);
      else
        $healthy[$property] = $data[$property];
    }

    return $healthy;
  }

  public function __construct(Entity $entity)
  {
    $this->entity = $entity;
  }

  /**
   * Method creates new record and returns a complete entity instance
   * @param array $entity_data
   * @return Entity: domain object instance
   */
  public static function create(array $entity_data): ?Entity
  {
    $record = static::sanitize($entity_data);

    $id = DB::getDB()->insert(static::$table, $record);
    if ($id) {
      $entity = new static::$entity_type($id);
      $entity->load($entity_data);
      return $entity;
    }

    return null;
  }

  /**
   * Method updates instance record if exists and creates it otherwise
   * @return bool: flag for success
   */
  public function save(): bool
  {
    $db = DB::getDB();

    if (!$id = $this->entity->getID())
      throw new IncompleteEntity(static::$entity_type);

    $info = [];
    foreach (static::$required as $property)
      $info[$property] = $this->entity->get($property);

    foreach (static::$optional as $property)
      $info[$property] = $this->entity->get($property);

    if ($result = $db->update(static::$table, $info, $id))
      $this->entity->setSync(true);

    return (bool) $result;
  }

  /**
   * Method loads record info into a entity instance
   * @param array $filters: db search filters
   * @return ?Entity: domain object instance if found
   * this method returns the first found object if many records matched
   */
  public static function get(array $filters = []): ?Entity
  {
    static::validateFilters($filters);

    if ($items = DB::getDB()->select('*', static::$table, $filters)) {
      $entity = new static::$entity_type($items[0]['id']);
      $entity->load($items[0]);
      $entity->setSync(true);
      return $entity;
    }
    return null;
  }

  /**
   * Method loads records' info into entity instances
   * @param array $filters: db search filters
   * @return Entities: matched records
   */
  public static function getAll(array $filters = []): Entities
  {
    static::validateFilters($filters);

    return new Entities(DB::getDB()->select('*', static::$table, $filters), static::$entity_type);
  }

  public static function validateFilters(array $filters)
  {
    foreach ($filters as $key => $value)
      if (!in_array($key, static::$required) && !in_array($key, static::$required) && !is_numeric($key) && $key != 'id')
        throw new InvalidArguments("Invalid Filter Key $key for entity of type " . static::$entity_type);
  }

  /**
   * Methods deletes the record corresponding to the current entity instance
   * @return bool: success flag
   */
  public function delete(): bool
  {
    $result = DB::getDB()->delete(static::$table, $this->entity->getID());
    if ($result)
      unset($this->entity);

    return (bool) $result;
  }
}
