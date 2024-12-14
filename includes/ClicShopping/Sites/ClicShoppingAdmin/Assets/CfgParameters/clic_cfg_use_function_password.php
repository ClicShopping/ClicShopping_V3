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
 * @param $password
 * @return array|string|string[]|null
 */
function clic_cfg_use_function_password($password)
{
  return preg_replace("|.|", "*", $password);
}