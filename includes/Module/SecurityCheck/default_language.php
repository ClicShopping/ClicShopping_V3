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

class securityCheck_default_language
{
  public string $type = 'danger';

  /**
   * Constructor method that initializes the language definitions for the SecurityCheck module.
   *
   * @return void
   */
  public function __construct()
  {
    $CLICSHOPPING_Language = Registry::get('Language');

    $CLICSHOPPING_Language->loadDefinitions('modules/SecurityCheck/default_language', null, null, 'Shop');
  }

  /**
   * Checks if the constant DEFAULT_LANGUAGE is defined.
   *
   * @return bool Returns true if DEFAULT_LANGUAGE is defined, otherwise false.
   */
  public function pass()
  {
    return defined('DEFAULT_LANGUAGE');
  }

  /**
   * Retrieves a predefined message indicating that no default language is defined.
   *
   * @return string The error message for no default language defined.
   */
  public function getMessage()
  {
    return CLICSHOPPING::getDef('error_no_default_language_defined');
  }
}