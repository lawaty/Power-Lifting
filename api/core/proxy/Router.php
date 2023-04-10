<?php

const ENDPOINTS_DIR = LOCAL.'/app/endpoints';

class Router
{
  private static function endpointExists($controller, $endpoint): bool
  {
    // Searching for endpoint
    return file_exists(ENDPOINTS_DIR."/$controller/$endpoint.php") || (!$endpoint && file_exists(ENDPOINTS_DIR."/$controller/index.php"));
  }

  public static function route(): ?Endpoint
  {
    // Creates Endpoint Object If Found
    $route = explode(API_DIR.'/', parse_url($_SERVER['REQUEST_URI'])['path'])[1];
    $sections = explode('/', explode('?', $route)[0]);
    $len = count($sections);
    if ($len != 1 && $len != 2)
      return null;

    $controller = strtolower($sections[0]);
    if ($len == 2) $endpoint = ucfirst($sections[1]);
    else $endpoint = null;

    if (!self::endpointExists($controller, $endpoint))
      return null;

    require(BASES_DIR."/Endpoint.php");

    if ($endpoint){
      require(ENDPOINTS_DIR."/$controller/$endpoint.php");
      return new $endpoint;
    }
    
    require(ENDPOINTS_DIR."/$controller/index.php");
    return new Get();
  }
}
