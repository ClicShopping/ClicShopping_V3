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

class securityCheck_default_currency
{
  public string $type = 'danger';

  /**
   * Constructor method.
   *
   * @return void
   */
  public function __construct()
  {
    $CLICSHOPPING_Language = Registry::get('Language');

    $CLICSHOPPING_Language->loadDefinitions('modules/SecurityCheck/default_currency', null, null, 'Shop');
  }

  /**
   * Checks if the constant 'DEFAULT_CURRENCY' is defined.
   *
   * @return bool True if 'DEFAULT_CURRENCY' is defined, false otherwise.
   */
  public function pass()
  {
    return defined('DEFAULT_CURRENCY');
  }

  /**
   * Retrieves the default error message for no default currency defined.
   *
   * @return string The error message indicating that no default currency is defined.
   */
  public function getMessage()
  {
    return CLICSHOPPING::getDef('error_no_default_currency_defined');
  }
}