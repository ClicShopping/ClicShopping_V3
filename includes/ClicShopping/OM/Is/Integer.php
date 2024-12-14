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
