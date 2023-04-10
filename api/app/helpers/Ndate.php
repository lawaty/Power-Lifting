<?php

class Ndate extends DateTime
{
  const DATE_TIME = "Y-m-d H:i";
  const DATE = "Y-m-d";
  const TIME = "H:i:s";

  public function __construct(string $date = 'now')
  {
    parent::__construct($date);
  }

  public static function now($format = self::DATE): string
  {
    return (new Ndate())->format($format);
  }

  public static function minutesUntil(Ndate $date): int
  {
    return self::secondsUntil($date) / 60;
  }

  public static function secondsUntil(Ndate $date): int
  {
    $diff = (new Ndate())->diff($date);
    $seconds = $diff->days * 24 * 3600 + $diff->h * 3600 + $diff->i * 60 + $diff->s;
    if (self::now() > $date)
      return $seconds * -1;

    return $seconds;
  }

  public function toMinutes(): int
  {
    return $this->format('H') * 60 + $this->format('i');
  }

  public function addDays(int $days): void
  {
    $interval = new DateInterval('P' . abs($days) . 'D');
    if ($days >= 0)
      $this->add($interval);
    else
      $this->sub($interval);
  }

  public function format($format = null): string
  {
    if (!$format)
      $format = self::DATE;

    return parent::format($format);
  }

  public function getWeekDates(): array
  {

    $dates = [];
    $temp = new Ndate($this->format());
    do {
      array_push($dates, $temp->format());
      $temp->addDays(-1);
    } while ($temp->format('D') != 'Fri');

    $temp = new Ndate($this->format());
    $temp->addDays(1);
    while ($temp->format('D') != 'Sat') {
      array_push($dates, $temp->format());
      $temp->addDays(1);
    }

    return $dates;
  }
}
