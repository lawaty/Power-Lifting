<?php

class Regex
{
  // Identifiers
  const ID = '/^[1-9][0-9]{0,10}$/u';
  const INT = '/^[0-9]{1,10}$/u';
  const SINT = '/^-?[0-9]{1,10}$/u';
  const ZERO_ONE = '/^[0-1]$/u';

  // Dates
  const MONTH = '/^([1-9]|1[0-2])$/u';
  const DATE = '/^(\d{4})-(\d{2})-(\d{2})$/u';
  const TIME = '/^[0-9]{2}:[0-9]{2}$/u';
  const DAY = '/^Sun|Mon|Tue|Wed|Thu|Fri|Sat$/u';
  const TIME_SEC = '/^[0-9]{2}:[0-9]{2}:[0-9]{2}$/u';
  const DATE_TIME = '/^(\d{4})-(\d{2})-(\d{2}) [0-9]{2}:[0-9]{2}$/u';
  const DATE_TIME_SEC = '/^(\d{4})-(\d{2})-(\d{2}) [0-9]{2}:[0-9]{2}:[0-9]{2}$/u';
  const DAY_TIME = '/^(Sun|Mon|Tue|Wed|Thu|Fri|Sat) [0-9]{2}:[0-9]{2}$/u';

  public static function generic($min, $max): string
  {
    return '/^' . self::GENERIC . '{' . $min . ',' . $max . '}$/u';
  }

  public static function separated(string $delimiter): string
  {
    return '/^'.self::GENERIC.'+('.$delimiter.self::GENERIC.'+)+$/u';
  }

  const JWT = '/^[\w-]*\.[\w-]*\.[\w-]*$/u';
  const LOGIN = '/^[\p{Arabic}\w\-. ]{6,40}$/u';
  const EMAIL = '/^[\w_\-.]+@[\w]+\.[\w]+$/u';
  const NAME = '/^[\p{Arabic}\w\-. ]{1,60}$/u';
  const PHONE = '/^[0-9\+]{5,17}$/u';
  const GRADE = '/^([1-9]|1[0-2])$/u';
  const HEX = '/^#([a-f0-9]{3}){1,2}\b$/i';

  // general
  const GENERIC = '[\p{Arabic}\w\-\)\(.,\s+]';
  const ANY = '/^(.|\s)+$/u';
}
