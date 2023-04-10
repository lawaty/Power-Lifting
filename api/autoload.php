<?php

const ENTITIES_DIR = LOCAL.'/app/model/entities';
const MAPPERS_DIR = LOCAL.'/app/model/mappers';
const HELPERS_DIR = LOCAL.'/app/helpers';
const LIB_DIR = LOCAL.'/core/lib';
const BASES_DIR = LOCAL.'/core/bases';
const EXCEPTIONS_DIR = LOCAL.'/core/exceptions';
const DB_DIR = LOCAL.'/core/database';

const DEPENDENCIES = [
  'DB' => DB_DIR,

  // Libraries
  'Firebase\JWT\JWT' => LIB_DIR."/php-jwt/src",
  'Firebase\JWT\Key' => LIB_DIR."/php-jwt/src",

  // Helpers
  'Validator' => HELPERS_DIR.'/validation',
  'ValidationUnit' => HELPERS_DIR.'/validation',
  'Curl' => HELPERS_DIR,
  'Regex' => HELPERS_DIR,
  'Ndate' => HELPERS_DIR,
  'Entities' => HELPERS_DIR,
  'AssocEntities' => HELPERS_DIR,

  // Bases and Interfaces
  'Authenticated' => BASES_DIR,
  'Entity' => BASES_DIR,
  'Mapper' => BASES_DIR,
  'JoinMapper' => BASES_DIR,

  // Exceptions
  'NegativeSectionReached' => EXCEPTIONS_DIR,
  'InvalidArguments' => EXCEPTIONS_DIR,

  'BadRequest' => EXCEPTIONS_DIR,
  'Forbidden' => EXCEPTIONS_DIR,
  'NotFound' => EXCEPTIONS_DIR,
  'NotModified' => EXCEPTIONS_DIR,
  'Old' => EXCEPTIONS_DIR,
  'NotMatching' => EXCEPTIONS_DIR,
  'Conflict' => EXCEPTIONS_DIR,
  'Fail' => EXCEPTIONS_DIR,

  'PropertyNotExisting' => EXCEPTIONS_DIR,
  'RequiredPropertyNotFound' => EXCEPTIONS_DIR,
  'IncompleteModel' => EXCEPTIONS_DIR,
  'InvalidID' => EXCEPTIONS_DIR,
  'IncompatibleModels' => EXCEPTIONS_DIR,

  'UniquenessViolated' => EXCEPTIONS_DIR,
  'ForeignKeyViolated' => EXCEPTIONS_DIR,

  // Model Classes
  'User' => ENTITIES_DIR,
  'UserMapper' => MAPPERS_DIR,
];

spl_autoload_register(function ($class_name) {
  $path = DEPENDENCIES[$class_name];
  $temp = explode('\\', $class_name);
  $file_name = end($temp);
  require "$path/$file_name.php";
});
