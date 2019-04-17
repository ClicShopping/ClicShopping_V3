<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\OM;

  use ClicShopping\OM\CLICSHOPPING;

  class DateTime {

   protected $datetime = false;

    protected $raw_pattern_date = 'Y-m-d';
    protected $raw_pattern_time = 'H:i:s';

    public function __construct($datetime, $use_raw_pattern = false, $strict = false)
    {
        if ($use_raw_pattern === false) {
         $pattern = CLICSHOPPING::getDef('date_time_format');
        } else {
          $pattern = $this->raw_pattern_date . ' ' . $this->raw_pattern_time;
        }

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

            if (($errors['warning_count'] > 0) || ($errors['error_count'] > 0)) {
                $this->datetime = false;

                $strict_log = true;
            }
        }

        if (($strict === true) && ($strict_log === true)) {
            trigger_error('DateTime: ' . $datetime . ' (' . $new_datetime . ') cannot be formatted to ' . $pattern);
        }
    }

    public function isValid()
    {
        return $this->datetime instanceof \DateTime;
    }

    public function get($pattern = null)
    {
        if (isset($pattern)) {
            return $this->datetime->format($pattern);
        }

        return $this->datetime;
    }

/*
 * Output a  date string in the selected locale date format
 * @param : $date,date format
 * @return string short date
 * $date needs to be in this format: YYYY-MM-DD HH:MM:SS
 */

    public function getShort($with_time = false)
    {
      $pattern = ($with_time === false) ? CLICSHOPPING::getDef('date_format_short') : CLICSHOPPING::getDef('date_time_format');
      return strftime($pattern, $this->getTimestamp());
    }


    public static function toShort($raw_datetime, $with_time = false, $strict = true)
    {
      $result = '';

      if (!empty($raw_datetime)) {
        $date = new DateTime($raw_datetime, true, $strict);

        if ($date->isValid()) {
          $pattern = ($with_time === false) ? CLICSHOPPING::getDef('date_format_short') : CLICSHOPPING::getDef('date_time_format');

          $result = strftime($pattern, $date->getTimestamp());
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

    public function getLong()
    {
        return strftime(CLICSHOPPING::getDef('date_format_long'), $this->getTimestamp());
    }

    public static function toLong($raw_datetime, $strict = true)
    {
        $result = '';

        $date = new DateTime($raw_datetime, true, $strict);

        if ($date->isValid()) {
          $result = strftime(CLICSHOPPING::getDef('date_format_long'), $date->getTimestamp());
        }

        return $result;
    }

    public function getRaw($with_time = true)
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
    public function getTimestamp()
    {
        return $this->datetime->getTimestamp();
    }

/**
 * Return an array of available time zones.
 *
 * @return array
 */

  public static function getTimeZones() {
      $time_zones_array = [];

      foreach (\DateTimeZone::listIdentifiers() as $id) {
          $tz_string = str_replace('_', ' ', $id);

          $id_array = explode('/', $tz_string, 2);

          $time_zones_array[$id_array[0]][$id] = isset($id_array[1]) ? $id_array[1] : $id_array[0];
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
 * Set the time zone to use for dates.
 *
 * @param string $time_zone An optional time zone to set to
 * @param string $site The Site to retrieve the time zone from
 * @return boolean
 */

   public static function setTimeZone($time_zone = null) {

    if (!isset($time_zone)) {
      $time_zone = CLICSHOPPING::configExists('time_zone') ? CLICSHOPPING::getConfig('time_zone') : date_default_timezone_get();
    }

    if ($time_zone === null || empty($time_zone)) {
      $time_zone = 'UTC';
    }

    return date_default_timezone_set($time_zone);
  }

/*
 * Output a date now
 * @param : $format,date format
 * @return string format date
 * $date needs to be in this format: YYYY-MM-DD HH:MM:SS
 */

    public static function getNow($format = null) {

      if (!isset($format)) {
        $format = CLICSHOPPING::getDef('date_format_long');
     }

      return date($format);
    }


/*
 * Output a  date reference  for invoice
 * @param : $date,date format
 * @return string short date reference
 * $date needs to be in this format: YYYYMMDD
 */
    public function getDateReferenceShort()
    {
      return strftime(CLICSHOPPING::getDef('date_format'), $this->getTimestamp());
    }

    public static function  toDateReferenceShort($raw_datetime, $strict = true) {

      $result = '';

      $date = new DateTime($raw_datetime, true, $strict);

      if ($date->isValid()) {
        $result = strftime(CLICSHOPPING::getDef('date_format'), $date->getTimestamp());
      }

      return $result;
    }


/*
* Date Unix Timestamp
* @param $timestamp, $format
* @return
*/

    public static function fromUnixTimestamp($timestamp, $format = null) {
      if (!isset($format)) {
        $format = CLICSHOPPING::getDef('date_format_long');
      }

      return date($format, $timestamp);
    }

/*
* Unix Date
* @param $year, date format
* @return true / false
*/

    public static function isLeapYear($year = null)  {

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

/*
 * Check date
 * @param
 * @return string short date
 * $date needs to be in this format: YYYY-MM-DD HH:MM:SS
 */

    public static function validate($date_to_check, $format_string, &$date_array) {
      $separator_idx = -1;

      $separators = ['-', ' ', '/', '.'];
      $month_abbr = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
      $no_of_days = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

      $format_string = strtolower($format_string);

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
 * Interval between 2 date
 *
 * @return string, date interval
 */
    public static function getIntervalDate($dateStart, $dateEnd, $differenceFormat = '%r%a') {

      $start = date_create($dateStart);
      $end = date_create($dateEnd);
      $interval = date_diff($start, $end);

      return $interval->format($differenceFormat);
    }
  }