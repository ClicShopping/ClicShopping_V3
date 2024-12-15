<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM;

use DateTimeZone;
use function count;
use function strlen;


/**
 * A class to handle date and time operations, providing functionalities
 * for formatting and conversion based on specific patterns and time zones.
 */
class DateTime
{
  protected $datetime = false;

  protected string $raw_pattern_date = 'Y-m-d';
  protected string $raw_pattern_time = 'H:i:s';

  /**
   * Constructs a DateTime object based on the provided parameters.
   *
   * @param string $datetime The date and time string to be converted to a DateTime object.
   * @param bool $use_raw_pattern Indicates whether to use a raw date and time pattern instead of the default format.
   * @param bool $strict Determines whether to trigger an error if the provided datetime cannot be formatted properly.
   * @return void
   */
  public function __construct(string $datetime, bool $use_raw_pattern = false, bool $strict = false)
  {
    if ($use_raw_pattern === false) {
      $pattern = CLICSHOPPING::getDef('date_time_format');
    } else {
      $pattern = $this->raw_pattern_date . ' ' . $this->raw_pattern_time;
    }

    $strict_log = false;

// format time as 00:00:00 if it is missing from the date
    $new_datetime = strtotime($datetime);

    if ($new_datetime !== false) {
      $new_datetime = date($pattern, $new_datetime);

      $this->datetime = \DateTime::createFromFormat($pattern, $new_datetime);

      $strict_log = false;
    }

    if ($this->datetime === false) {
      $strict_log = true;
    } else {
      $errors = \DateTime::getLastErrors();

      if (is_array($errors)) {
        if (($errors['warning_count'] > 0) || ($errors['error_count'] > 0)) {
          $this->datetime = false;

          $strict_log = true;
        }
      }
    }

    if (($strict === true) && ($strict_log === true)) {
      trigger_error('DateTime: ' . $datetime . ' (' . $new_datetime . ') cannot be formatted to ' . $pattern);
    }
  }

  /**
   * Checks if the current object's datetime property is a valid DateTime instance.
   *
   * @return bool True if the datetime property is an instance of \DateTime, false otherwise.
   */
  public function isValid(): bool
  {
    return $this->datetime instanceof \DateTime;
  }

  /**
   * Retrieves the formatted date and time as a string based on the provided pattern.
   * If no pattern is provided, it returns the raw datetime object as a string.
   *
   * @param string|null $pattern The format pattern for the datetime string, or null to return the raw datetime.
   * @return bool|string The formatted datetime string or the raw datetime.
   */
  public function get(string|null $pattern = null): bool|string
  {
    if (isset($pattern)) {
      return $this->datetime->format($pattern);
    }

    return $this->datetime;
  }

  /**
   * Generates a formatted date string based on the specified configuration.
   *
   * @param bool $with_time Determines whether the output includes time information. If false, only the date is returned.
   * @return string The formatted date or date-time string.
   */

  public function getShort(bool $with_time = false): string
  {
    $pattern = ($with_time === false) ? CLICSHOPPING::getDef('date_format_short') : CLICSHOPPING::getDef('date_time_format');

    $date = new DateTime($pattern, true, true);

    return date($pattern, $date->getTimestamp());
  }

  /**
   * Converts a raw datetime string into a formatted short or long date string.
   *
   * @param string $raw_datetime The raw datetime string to be converted.
   * @param bool $with_time Whether to include the time in the formatted result. Defaults to false.
   * @param bool $strict Whether to enable strict validation when parsing the datetime. Defaults to true.
   * @return string The formatted date string, with or without time, based on the input parameters.
   */
  public static function toShort(string $raw_datetime, bool $with_time = false, bool $strict = true): string
  {
    $result = '';

    if (!empty($raw_datetime)) {
      $date = new DateTime($raw_datetime, true, $strict);

      if ($date->isValid()) {
        $pattern = ($with_time === false) ? CLICSHOPPING::getDef('date_format_short') : CLICSHOPPING::getDef('date_format_long');

        $result = date($pattern, $date->getTimestamp());
      }
    }

    return $result;
  }

  /**
   * Converts a raw datetime string into a formatted date string without custom formatting.
   *
   * @param string $raw_datetime The raw datetime string to be converted.
   * @param bool $with_time Indicates whether the result should include time. Defaults to false.
   * @param bool $strict Controls whether strict validation is applied to the datetime. Defaults to true.
   * @return string The formatted date string or an empty string if the input is invalid.
   */
  public static function toShortWithoutFormat(string $raw_datetime, bool $with_time = false, bool $strict = true): string
  {
    $result = '';

    if (!empty($raw_datetime)) {
      $date = new DateTime($raw_datetime, true, $strict);

      if ($date->isValid()) {
        $pattern = ($with_time === false) ? CLICSHOPPING::getDef('date_format_short_sql') : CLICSHOPPING::getDef('date_time_format');

        $result = date($pattern, $date->getTimestamp());
      }
    }

    return $result;
  }

  /*
   * Output a  date string in the selected locale date format
   * @param : $date,date format
   * @return string long date
   * $date needs to be in this format: YYYY-MM-DD HH:MM:SS
    * $date needs to be in this format: Saturday february 2015
   */

  /**
   * Retrieves the formatted date string based on the long date format.
   *
   * @return string Returns the formatted date string.
   */
  public function getLong(): string
  {
    $pattern = new DateTime(CLICSHOPPING::getDef('date_format_long'), true, true);

    return date($pattern, $this->getTimestamp());
  }

  /**
   * Converts a raw datetime string into a formatted long date string.
   *
   * @param string $raw_datetime The raw datetime string to be converted.
   * @param bool $strict Optional. If set to true, applies strict validation on the datetime string. Defaults to true.
   * @return string The formatted long date string, or an empty string if the datetime string is invalid.
   */
  public static function toLong(string $raw_datetime, bool $strict = true): string
  {
    $result = '';

    $date = new DateTime($raw_datetime, true, $strict);

    if ($date->isValid()) {
      $result = date(CLICSHOPPING::getDef('date_format_long'), $date->getTimestamp());
    }

    return $result;
  }

  /**
   * Retrieves the raw formatted date and optionally the time based on the provided pattern.
   *
   * @param bool $with_time Determines whether the time should be included in the raw output. Defaults to true.
   * @return string The raw formatted date and/or time as a string.
   */
  public function getRaw(bool $with_time = true): string
  {
    $pattern = $this->raw_pattern_date;

    if ($with_time === true) {
      $pattern .= ' ' . $this->raw_pattern_time;
    }

    return $this->datetime->format($pattern);
  }

  /*
  * Date Timestamp
  * @param $date
  * @return
  * ex : 1430965442
  */
  /**
   * Retrieves the Unix timestamp from the stored DateTime object.
   *
   * @return int The Unix timestamp representing the date and time.
   */
  public function getTimestamp(): int
  {
    return $this->datetime->getTimestamp();
  }

  /**
   * Retrieves a list of time zones formatted into an array containing their identifiers, display text, and groupings.
   *
   * @return array An array of time zone data, where each element is an associative array with keys:
   *               - 'id': The identifier of the time zone.
   *               - 'text': A human-readable representation of the time zone.
   *               - 'group': The group/category the time zone belongs to.
   */

  public static function getTimeZones(): array
  {
    $time_zones_array = [];

    foreach (DateTimeZone::listIdentifiers() as $id) {
      $tz_string = str_replace('_', ' ', $id);

      $id_array = explode('/', $tz_string, 2);

      $time_zones_array[$id_array[0]][$id] = $id_array[1] ?? $id_array[0];
    }

    $result = [];

    foreach ($time_zones_array as $zone => $zones_array) {
      foreach ($zones_array as $key => $value) {
        $result[] = [
          'id' => $key,
          'text' => $value,
          'group' => $zone
        ];
      }
    }

    return $result;
  }

  /**
   * Sets the time zone for the application.
   *
   * @param string|null $time_zone The desired time zone to set. If null or empty, falls back to configuration or UTC.
   * @return bool Returns true on success or false on failure.
   */

  public static function setTimeZone(string|null $time_zone = null): bool
  {

    if (!isset($time_zone)) {
      $time_zone = CLICSHOPPING::configExists('time_zone') ? CLICSHOPPING::getConfig('time_zone') : date_default_timezone_get();
    }

    if ($time_zone === null || empty($time_zone)) {
      $time_zone = 'UTC';
    }

    return date_default_timezone_set($time_zone);
  }

  /**
   * Retrieves the current date and time formatted according to the specified format.
   *
   * @param string|null $format The date format to use. If null, a default long date format is applied.
   * @return string The formatted current date and time.
   */
  public static function getNow(string|null $format = null): string
  {

    if (!isset($format)) {
      $format = CLICSHOPPING::getDef('date_format_long');
    }

    return date($format);
  }

  /**
   * Retrieves a short date reference string formatted according to the defined date format.
   *
   * @return string The formatted date string based on the given pattern and timestamp.
   */
  public function getDateReferenceShort(): string
  {
    $pattern = new DateTime(CLICSHOPPING::getDef('date_format'), true, true);

    return date($pattern, $this->getTimestamp());
  }

  /**
   * Converts a raw datetime string into a formatted short date reference.
   *
   * @param string $raw_datetime The raw datetime string to be converted.
   * @param bool $strict Specifies whether to apply strict parsing of the input datetime.
   * @return string The formatted short date reference. Returns an empty string if the datetime is invalid or empty.
   */
  public static function toDateReferenceShort(string $raw_datetime, bool $strict = true): string
  {
    $result = '';

    if (!empty($raw_datetime)) {
      $date = new DateTime($raw_datetime, true, $strict);

      if ($date->isValid()) {
        $pattern = CLICSHOPPING::getDef('date_invoice');
        $result = date($pattern, $date->getTimestamp());
      }
    }

    return $result;
  }

  /**
   * Converts a Unix timestamp into a formatted date string.
   *
   * @param string $timestamp The Unix timestamp to convert.
   * @param mixed $format Optional. The format in which the date should be returned. If not provided, a default format will be used.
   * @return string The formatted date string.
   */
  public static function fromUnixTimestamp(string $timestamp, $format = null): string
  {
    if (!isset($format)) {
      $format = CLICSHOPPING::getDef('date_format_long');
    }

    return date($format, $timestamp);
  }

  /**
   * Determines if the given year is a leap year.
   *
   * A leap year is a year divisible by 4. However, years divisible by 100 are not leap years unless they are also divisible by 400.
   *
   * @param string|null $year The year to check. If null, the current year is used.
   * @return bool True if the year is a leap year; false otherwise.
   */
  public static function isLeapYear(?string $year = null): bool
  {

    if (!isset($year)) {
      $year = self::getNow('Y');
    }

    if ($year % 100 == 0) {
      if ($year % 400 == 0) {
        return true;
      }
    } else {
      if (($year % 4) == 0) {
        return true;
      }
    }

    return false;
  }

  /**
   * Validates a given date string against a specific format and populates an array with the extracted date components.
   *
   * @param string $date_to_check The date string to validate.
   * @param string $format_string The format string describing the expected date format (e.g., 'dd-mm-yyyy').
   * @param array &$date_array Reference to an array that will be populated with the extracted date components (year, month, day) if validation succeeds.
   * @return bool Returns true if the date is valid according to the provided format, false otherwise.
   */
  public static function validate(string $date_to_check, string $format_string, array &$date_array): bool
  {
    $separators = ['-', ' ', '/', '.'];
    $month_abbr = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
    $no_of_days = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

    $format_string = mb_strtolower($format_string);

    if (strlen($date_to_check) != strlen($format_string)) {
      return false;
    }

    $size = count($separators);

    for ($i = 0; $i < $size; $i++) {
      $pos_separator = strpos($date_to_check, $separators[$i]);

      if ($pos_separator !== false) {
        $date_separator_idx = $i;
        break;
      }
    }

    for ($i = 0; $i < $size; $i++) {
      $pos_separator = strpos($format_string, $separators[$i]);

      if ($pos_separator !== false) {
        $format_separator_idx = $i;
        break;
      }
    }

    if ($date_separator_idx != $format_separator_idx) {
      return false;
    }

    if ($date_separator_idx != -1) {
      $format_string_array = explode($separators[$date_separator_idx], $format_string);

      if (count($format_string_array) != 3) {
        return false;
      }

      $date_to_check_array = explode($separators[$date_separator_idx], $date_to_check);

      if (count($date_to_check_array) != 3) {
        return false;
      }

      $size = count($format_string_array);

      for ($i = 0; $i < $size; $i++) {
        if ($format_string_array[$i] == 'mm' || $format_string_array[$i] == 'mmm') $month = $date_to_check_array[$i];
        if ($format_string_array[$i] == 'dd') $day = $date_to_check_array[$i];
        if (($format_string_array[$i] == 'yyyy') || ($format_string_array[$i] == 'aaaa')) $year = $date_to_check_array[$i];
      }
    } else {
      if (strlen($format_string) == 8 || strlen($format_string) == 9) {
        $pos_month = strpos($format_string, 'mmm');

        if ($pos_month !== false) {
          $month = substr($date_to_check, $pos_month, 3);
          $size = count($month_abbr);

          for ($i = 0; $i < $size; $i++) {
            if ($month == $month_abbr[$i]) {
              $month = $i;
              break;
            }
          }
        } else {
          $month = substr($date_to_check, strpos($format_string, 'mm'), 2);
        }
      } else {
        return false;
      }

      $day = substr($date_to_check, strpos($format_string, 'dd'), 2);
      $year = substr($date_to_check, strpos($format_string, 'yyyy'), 4);
    }

    if (strlen($year) != 4) {
      return false;
    }

    if (!settype($year, 'integer') || !settype($month, 'integer') || !settype($day, 'integer')) {
      return false;
    }

    if ($month > 12 || $month < 1) {
      return false;
    }

    if ($day < 1) {
      return false;
    }

    if (self::isLeapYear($year)) {
      $no_of_days[1] = 29;
    }

    if ($day > $no_of_days[$month - 1]) {
      return false;
    }

    $date_array = [$year, $month, $day];

    return true;
  }

  /**
   * Calculates the interval between two dates and formats it according to the specified difference format.
   *
   * @param string $dateStart The start date in a valid date format.
   * @param string $dateEnd The end date in a valid date format.
   * @param string $differenceFormat The format in which the difference should be returned. Default is '%r%a'.
   * @return string The formatted date interval as a string.
   */
  public static function getIntervalDate(string $dateStart, string $dateEnd, string $differenceFormat = '%r%a'): string
  {
    $start = date_create($dateStart);
    $end = date_create($dateEnd);
    $interval = date_diff($start, $end);

    return $interval->format($differenceFormat);
  }
}