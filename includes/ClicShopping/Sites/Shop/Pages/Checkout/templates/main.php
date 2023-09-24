<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

$CLICSHOPPING_Customer = Registry::get('Customer');

if (!$CLICSHOPPING_Customer->isLoggedOn()) {
  CLICSHOPPING::redirect(null, 'Account&LogIn');
}
