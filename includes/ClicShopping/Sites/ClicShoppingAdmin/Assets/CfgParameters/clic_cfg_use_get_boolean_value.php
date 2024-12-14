<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */
/**
 * @param $string
 * @return bool|mixed|string
 */
function clic_cfg_use_get_boolean_value($string)
{
  switch ($string) {
    case -1:
    case '-1':
      return false;

    case 0:
    case '0':
      return 'optional';

    case 1:
    case '1':
      return true;

    default:
      return $string;
  }
}