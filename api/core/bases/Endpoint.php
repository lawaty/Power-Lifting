<?php

abstract class Endpoint
{
  /**
   * Generic blueprint for all endpoints
   */
  public $request;
  private $validator;

  protected function init($expect, $request)
  {
    /**
     * Initializing endpoint
     * @param expect: expected structure of a healthy request
     * @param request: the request body
     */

    $this->request = $request;
    $this->validator = new Validator($expect, static::class);
  }

  protected function prehandle(): ?Response
  {
    if ($invalid = $this->validator->validate($this->request))
      return new Response(BAD_REQUEST, "Invalid Parameter: $invalid");

    $this->request = $this->validator->getFiltered();
    return null;
  }

  abstract protected function handle(): Response;

  public function run(): Response
  {
    if ($response = $this->prehandle())
      return $response;

    return $this->handle();
  }

  public function expects($param)
  {
    return isset($this->request[$param]);
  }

  public function postHandle(): void
  {
  }

  public function getUser(): ?User
  {
    /**
     * Returns the request sender's ID
     * Overridden in Authenticated Base Class
     */
    return null;
  }
}
