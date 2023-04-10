<?php

abstract class JoinMapper extends Mapper implements Mapper
{
  protected static array $joined_mappers;

  public static function get(array $filters = []): ?Entity
  {
    /**
     * Method loads record info into a model instance
     * @param array $filters: db search filters
     * @return ?Entity: domain object instance if found
     * @note: this method returns the first found object if many records matched
     */
    static::validateFilters($filters);

    if ($items = DB::getDB()->select(
      static::formatFields(),
      static::formatTables(),
      $filters
    )) {
      $model = new static::$entity_type($items[0]);
      $model->load($items[0]);
      $model->setSync(true);
      return $model;
    }
    return null;
  }

  public static function getAll(array $filters = []): Entities
  {
    /**
     * Method loads records' info into model instances
     * @param array $filters: db search filters
     * @return array: matched records
     */
    self::validateFilters($filters);

    if (is_array($filters) && !count($filters))
      $filters = [];

    return new Entities(DB::getDB()->select(static::formatFields(), static::formatTables(), $filters), static::$entity_type);
  }

  protected static function formatFields(): array
  {
    $fields = [];
    foreach (static::$joined_mappers as $mapper) { // formatting every mapper record fields
      $mapper_info = $mapper::getRecordInfo();
      $table = $mapper::getTableName();
      foreach ($mapper_info as $field) // formating every field for a mapper record
        array_push($fields, "$table.$field");
    }

    return $fields;
  }

  protected static function formatTables(): string
  {
    $table1 = static::$joined_mappers[0]::getTableName();
    $table2 = static::$joined_mappers[1]::getTableName();
    $table3 = static::$joined_mappers[2]::getTableName();

    $foreign1 = rtrim($table1, 's') . '_id';
    $foreign2 = rtrim($table3, 's') . '_id';

    return "$table1 join $table2 join $table3 on $table1.id = $table2.$foreign1 and $table3.id = $table2.$foreign2";
  }

  public static function validateFilters(array $filters)
  {
    foreach ($filters as $filter => $value) {
      if (!str_contains($filter, ".")) {
        $new_filter = preg_replace("/_id/", "s.id", $filter);
        $filters[$new_filter] = $value;
        unset($filters[$filter]);
      }
    }

    foreach ($filters as $key => $value) {
      $mapper = ucfirst(rtrim(explode('.', $key)[0], "s")) . "Mapper";
      $key = explode('.', $key)[1];
      if (!in_array($key, $mapper::getRecordInfo()) && !is_numeric($key) && $key != 'id')
        throw new InvalidArguments("Invalid Filter Key $key");
    }
  }
}
