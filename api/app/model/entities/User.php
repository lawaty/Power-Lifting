<?php

class User extends Entity {
  protected static string $mapper_type = 'UserMapper';
  
  protected string $username;
  protected string $password;
  protected string $email;
}