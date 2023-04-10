<?php

class Create extends Endpoint {
  public function __construct()
  {
    $this->init([
      'username' => [true, Regex::LOGIN],
      'password' => [true, Regex::LOGIN],
      'email' => [true, Regex::EMAIL]
    ], $_POST);
  }

  public function handle(): Response
  {
    return new Response(SUCCESS, UserMapper::create($this->request));
  }
}