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

/**
 * Handles the security check for the PHP session.auto_start configuration setting.
 *
 * Ensures that session.auto_start is disabled for proper functionality and security.
 */
class securityCheck_session_auto_start
{
  public string $type = 'warning';

  /**
   * Initializes the class by loading the required language definitions for the session_auto_start security check module.
   *
   * @return void
   */
  public function __construct()
  {
    $CLICSHOPPING_Language = Registry::get('Language');

    $CLICSHOPPING_Language->loadDefinitions('modules/SecurityCheck/session_auto_start', null, null, 'Shop');
  }

  /**
   * Checks if the PHP session.auto_start setting is disabled.
   *
   * @return bool Returns true if session.auto_start is disabled, false otherwise.
   */
  public function pass()
  {
    return ((bool)ini_get('session.auto_start') === false);
  }

  /**
   * Retrieves a predefined warning message related to session auto start.
   *
   * @return string The warning message.
   */
  public function getMessage()
  {
    return CLICSHOPPING::getDef('warning_session_auto_start');
  }
}