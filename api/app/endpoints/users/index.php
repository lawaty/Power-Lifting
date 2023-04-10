<?php

class Get extends Endpoint {
  public function __construct()
  {
    $this->init([], $_GET);
  }

  public function handle(): Response
  {
    $users = UserMapper::getAll();
    
    $response_code = SUCCESS;
    return new Response($response_code, $users);
  }
}