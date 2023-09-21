<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\HTML;

function clic_cfg_set_textarea_field($text, $key = null)
{
  $name = (!empty($key) ? 'configuration[' . $key . ']' : 'configuration_value');

  return HTML::textAreaField($name, $text, 35, 5);
}