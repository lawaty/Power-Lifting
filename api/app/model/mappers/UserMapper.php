<?php

class UserMapper extends Mapper
{
  protected static string $table = "users";
  protected static string $entity_type = "User";
  public static array $required = ['username', 'password', 'email'];
}
