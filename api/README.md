### Architecture Specifications
<br>

This architecture aims to simplify the endpoints reproductivity and maintainability.

Let's view the file hierarchy at first.

<br>

# FHS
## config.php
- Defines macros, and globals we need to access everywhere
- Contains framework setup arguments

## proxy
Contains the proxy belongings

## endpoints directory
Contains the endpoints' definitions

## models
Model (or domain objects) definitions distributed in a modular file structure

## mappers
Contains mappers for every model that carry over the **persistence** mappers of objects

## services
Contains service providers that represent the real logic in the system

## database directory
- Contains DB.php which is a `wrapper over PDO` sql library that aims to easier the db communication by providing complex `sql query automatic formatting`

## helpers
- Helper Classes (Validator, CurlProvider, Regex, etc...)

## lib
Third-party libraries

<br>
<br>

# How the program executed

The whole program starts inside the index.php file. This file cooperates with Logger, Router, and Endpoint instances to finally deliver the response.

## Logger
Logger is responsible for `logging ALL REQUESTS` and `analyzing` them to add a comment on each.

## Router
The router `parses the url and searches` for the endpoint. It returns `404` error code if not found and `returns the endpoint instance` otherwise.

The proxy continues by running the `run()` method inside the desired endpoint which returens a result containing  `responseCode and responseBody`. Finally it passes the response `to the client in JSON format`.

<br>
Now, let's see the representation of endpoint in our architecture and how to create new, edit existing, and maintain the underlying foundation.

<br>

# What an endpoint represents in this architecture ?
Every endpoint is a concrete class extends one of the base classes and defines an `expect` for validation and `handle()` method. Both `expect`, and `handle()` are unique and that's why we define them for every endpoint.

`expect` is represented as an array of expected params with validation criteria and separated into three sections: `required` parameters, `optional` parameters, and `lists` parameters.

`lists` parameters are those parameters that contain unknown number of items and we need to validate each item, so we define a generic expect for all items in the list the validation criteria validates all.

> `expect` can have any combination of required, optional, and lists and `does not require` any of them.

```php
$expect = array(
  'required'=>array(
    'param1'=>'validation regex',
    'param2'=>'protected function returning bool value and accepts one parameter which is param2'
  ),
  'optional'=>"same",

  'lists'=>array(
    'list1'=>array(
      'item1'=>'validation regex',
      'item2'=>'protected function'
    ),
    'list2'=>array(
      'item1'=>'validation regex',
      'item2'=>'protected function'
    ),  )
);
```

Since validation algorithms in addtion to others are common among all endpoints and conceptually belong to them, `abstract base endpoint classes` are created to take over these common responsibilities. abstract base classses are defined inside controllers/Endpoint.php.

<br>
<br>

# Abstract base classes
A `prehandle()` method is defined and unique for every abstract base class and is fired by the `run()` method.

`run()` is a mehtod defined for `all endpoint base classes` and just fires prehandle and then handle if prehandle has not raised any issues.

Abstract base classes aim to successfully fire the `prehandle()` independent of any other classes.

Abstract base classes can be extended to define the `expect`, and `handle()` to create a new concrete endpoint.

> Note: prehandle is defined inside base classes while handle is defined inside the concrete endpoint class

<br>

## Base Class 1: Endpoint
This is the very basic base class that the functionality inside must be found for **ALL ENDPOINTS**. It takes over the following responseibilities

- Storing endpoint belongings
- Validating expects
- Formatting Response
- The simplest prehandle() method.
- "run()" method

### `Storing endpoint belongings`
Endpoint base class contains the method `init()` which accepts a `expect` and a `request` parameters

### `Validating expect`
Endpoint base class containse the method `validateParams` which checks that the request `matches the defined expect` and returns the unmatching parameter if found and null otherwise.

### `Formatting Mappernse`
method `format()` accepts an `error code` and `response body` then formats and returns them to the layer concerned with output.

### `prehandle() method`
prehandle in this base class just calls the `validating algorithms and formats` a response if `bad request` is identified.

### `run() method`
run method runs the `prehandle and then handle` if prehandle hasn't found any issues. 

> This endpoint is convenient for all endpoints that do not require authentication

<br>

## Base Class 2: Authenticated
This class `extends Endpoint` and `overrides prehandle` to add the authentication layer `after validation`. Everything else is still the same.

<br>

> This endpoint is convenient for all endpoints that require authentication

<br>

## Concrete Endpoint abstraction

<br>

> Note: while implementing `handle`, you have the request parameters clean and stored inside $this->request protected member.

<br>

```php
class EndpointName extends SomeBaseClass {
  public function __construct(){
    $expect = array("as described above");
    $request = $_POST // can be anything that match your needs
    $this->init($expect, $request);
  }

  public function handle(){
    // You can assume here that prehandle has been fired & just implement the main action of this endpoint.`
    ... 
    if($you_want_to_return_response_and_exit)
      return $this->format(ERROR_CODE, $responseBody); // responseBody can be string, or array and it is json_encoded at the end of the execution
    
    // This function MUST RETURN response code and body using the format method for all use cases not to cause program crashes
  }
}
```