<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

abstract class Authenticated extends Endpoint
{
  /**
   * Endpoints that require user authentication
   */
  protected $user = null;

  protected function init($expect, $request)
  {
    /**
     * Add token to expected array
     * @param array $expect: expectation array
     * @param array $request: request array to be validated
     */
    $expect['token'] = [true, Regex::JWT];
    parent::init($expect, $request);
  }

  protected function prehandle(): ?Response
  {
    /**
     * Authenticate user if request is good
     */
    if ($response = parent::prehandle())
      return $response;

    if ($user = self::auth($this->request['token']))
      $this->user = $user;

    if (!$this->user)
      return new Response(UNAUTHORIZED);

    unset($this->request['token']);
    return null;
  }

  public function getUser(): ?User
  {
    /**
     * Returns the request sender's ID
     */
    
    if($this->user)
      return $this->user;
      
    return null;
  }

  public static function auth(string $sent_token)
  {
    if ($data = self::decode($sent_token))
      return new User($data->id);

    else return false;
  }

  public static function getToken(User $user) {
    return self::encode([
      'id' => $user->get('id')
    ]);
  }

  public static function encode($data)
  {
    return JWT::encode($data, SECRET, 'HS256');
  }

  public static function decode(string $cypher)
  {
    try {
      return JWT::decode($cypher, new Key(SECRET, 'HS256'), ['decodeAsArray' => true]);
    }
    catch (DomainException $e){
      return false;
    }
  }
}
