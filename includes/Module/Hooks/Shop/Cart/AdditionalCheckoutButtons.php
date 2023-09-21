<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Module\Hooks\Shop\Cart;

use ClicShopping\OM\Registry;
use ClicShopping\Sites\Shop\Payment;

class AdditionalCheckoutButtons
{

  public function execute()
  {

    if (!Registry::exists('Payment')) {
      Registry::set('Payment', new Payment());
    }

    $CLICSHOPPING_Payment = Registry::get('Payment');

    if (isset($CLICSHOPPING_Payment)) {
      return implode('', $CLICSHOPPING_Payment->checkout_initialization_method());
    }
  }

}
