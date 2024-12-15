<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Is;

/**
 * Integer class provides a static method to validate if a given value is an integer.
 * It also allows for optional minimum and maximum range validation.
 */

class Integer implements \ClicShopping\OM\IsInterface
{
  /**
   * Validates whether the given value is an integer within the optional specified range.
   *
   * @param mixed $value The value to validate.
   * @param int|null $min Optional. The minimum allowable value, inclusive.
   * @param int|null $max Optional. The maximum allowable value, inclusive.
   * @return bool Returns true if the value is a valid integer within the range, otherwise false.
   */
  public static function execute($value, int $min = null, int $max = null): bool
  {
    $options = [];

    if (isset($min)) {
      $options['options']['min_range'] = $min;
    }

    if (isset($max)) {
      $options['options']['max_range'] = $max;
    }

    return filter_var($value, \FILTER_VALIDATE_INT, $options) !== false;
  }
}
