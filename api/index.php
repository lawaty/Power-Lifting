<?php

require "config.php";
require "autoload.php";
require "functions.php";

require "core/proxy/Logger.php";
require "core/proxy/Router.php";
require "core/proxy/Response.php";

ob_start();

$logger = new Logger();

if (!($endpoint = Router::route())) {
  $response = new Response(NOT_FOUND, 'Endpoint Not Found');
} else {
  try {
    $response = $endpoint->run();
  } catch (Exception $e) {
    $code = is_integer($e->getCode()) ? $e->getCode() : FAIL;
    $response = new Response($code, $e->getMessage());

    if(DEBUG)
      $response = new Response($code, trace($e));
  }
}

$response->echo();

if($endpoint instanceof Endpoint)
  $endpoint->postHandle();

$logger->log($endpoint, $response);