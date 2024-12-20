<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Antispam\Module\Hooks\Shop\Account\CreatePro;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\Antispam\Antispam as AntispamApp;
use ClicShopping\Apps\Configuration\Antispam\Classes\Shop\AntiSpam;

class PreAction implements \ClicShopping\OM\Modules\HooksInterface
{
  public mixed $app;
  public mixed $messageStack;

  /**
   * Constructor method that initializes the Antispam application and message stack.
   *
   * Ensures the Antispam application is registered in the system registry. If not already
   * registered, it creates and registers an instance of AntispamApp. Retrieves and stores
   * the Antispam app instance and message stack for use within the class.
   *
   * @return void
   */
  public function __construct()
  {
    if (!Registry::exists('Antispam')) {
      Registry::set('Antispam', new AntispamApp());
    }

    $this->app = Registry::get('Antispam');
    $this->messageStack = Registry::get('MessageStack');
  }

  /**
   * Checks the invisible antispam mechanism during the account creation process.
   *
   * @return bool Returns true if an error is detected with the invisible antispam check, false otherwise.
   */
  private static function checkInvisibleAntispam(): bool
  {
    $error = false;

    if (!\defined('CLICSHOPPING_APP_ANTISPAM_IN_CREATE_ACCOUNT_PRO') || CLICSHOPPING_APP_ANTISPAM_IN_CREATE_ACCOUNT_PRO == 'False') {
      return false;
    }

    if (!\defined('CLICSHOPPING_APP_ANTISPAM_IN_STATUS') || CLICSHOPPING_APP_ANTISPAM_IN_STATUS == 'False') {
      return false;
    }

    if (CLICSHOPPING_APP_ANTISPAM_IN_CREATE_ACCOUNT_PRO == 'True' && !isset($_POST['invisible_clicshopping'])) {
      $error = true;
    }

    return $error;
  }

  /**
   * Checks numeric antispam conditions based on predefined constants.
   *
   * Validates if specific antispam-related application settings are enabled.
   * If the settings are enabled, it proceeds to perform a numeric antispam check.
   *
   * @return bool Returns true if the numeric antispam check passes, false otherwise or if the settings are disabled.
   */
  private static function checkNumericAntispam(): bool
  {
    if (!\defined('CLICSHOPPING_APP_ANTISPAM_AM_TELL_A_FRIEND') || CLICSHOPPING_APP_ANTISPAM_AM_TELL_A_FRIEND == 'False') {
      return false;
    }

    if (!\defined('CLICSHOPPING_APP_ANTISPAM_AM_CREATE_ACCOUNT_PRO') || CLICSHOPPING_APP_ANTISPAM_AM_CREATE_ACCOUNT_PRO == 'False') {
      return false;
    }

    $error = AntiSpam::checkNumericAntiSpam();

    return $error;
  }

  /**
   * Executes the antispam checks on the account creation process.
   *
   * This method verifies if the antispam checks are enabled and performs
   * validation against invisible and numeric antispam mechanisms. If any
   * of the antispam checks fail, an error message is added to the message
   * stack and a redirect is initiated.
   *
   * @return bool Returns false if the antispam app is disabled or validation is not applicable.
   */
  public function execute()
  {
    if (!\defined('CLICSHOPPING_APP_ANTISPAM_STATUS') || CLICSHOPPING_APP_ANTISPAM_STATUS == 'False') {
      return false;
    }

    if (isset($_GET['Account'], $_GET['Create'], $_GET['Process'])) {
      $error = false;

      $error_invisible = static::checkInvisibleAntispam();
      $error_numeric = static::checkNumericAntispam();

      if ($error_invisible === true || $error_numeric === true) {
        $error = true;
      }

      if ($error === true) {
        $this->messageStack->add(CLICSHOPPING::getDef('text_error_antispam'), 'error');
        CLICSHOPPING::redirect(null, 'Account&CreatePro');
      }
    }
  }
}