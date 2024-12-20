<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Antispam\Module\Hooks\Shop\Products\TellAFriend;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Modules\HooksInterface;
use ClicShopping\OM\Registry;
use function defined;

use ClicShopping\Apps\Configuration\Antispam\Antispam as AntispamApp;
use ClicShopping\Apps\Configuration\Antispam\Classes\Shop\AntiSpam;

class PreAction implements HooksInterface
{
  public mixed $app;
  public mixed $messageStack;

  /**
   * Initializes the class by checking for the existence of the 'Antispam' key in the Registry.
   * If the key does not exist, it sets a new instance of AntispamApp in the Registry.
   * Retrieves and assigns the 'Antispam' app and 'MessageStack' from the Registry.
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
   * Checks the invisible antispam conditions in the application.
   *
   * @return bool Returns true if the antispam conditions are not met; false otherwise.
   */
  private static function checkInvisibleAntispam(): bool
  {
    $error = false;

    if (!defined('CLICSHOPPING_APP_ANTISPAM_IN_TELL_A_FRIEND') || CLICSHOPPING_APP_ANTISPAM_IN_TELL_A_FRIEND == 'False') {
      return false;
    }

    if (!defined('CLICSHOPPING_APP_ANTISPAM_IN_STATUS') || CLICSHOPPING_APP_ANTISPAM_IN_STATUS == 'False') {
      return false;
    }

    if (CLICSHOPPING_APP_ANTISPAM_IN_TELL_A_FRIEND == 'True' && !isset($_POST['invisible_clicshopping'])) {
      $error = true;
    }

    return $error;
  }

  /**
   * Checks if the numeric antispam mechanism is enabled and operational based on defined settings.
   *
   * @return bool Returns true if numeric antispam passed without error, false otherwise.
   */
  private static function checkNumericAntispam(): bool
  {
    if (!defined('CLICSHOPPING_APP_ANTISPAM_AM_TELL_A_FRIEND') || CLICSHOPPING_APP_ANTISPAM_AM_TELL_A_FRIEND == 'False') {
      return false;
    }

    if (!defined('CLICSHOPPING_APP_ANTISPAM_AM_STATUS') || CLICSHOPPING_APP_ANTISPAM_AM_STATUS == 'False') {
      return false;
    }

    $error = AntiSpam::checkNumericAntiSpam();

    return $error;
  }

  /**
   * Executes the antispam validation logic.
   *
   * This method checks if the Antispam application is enabled and validates
   * antispam measures for the "Tell a Friend" process. If any antispam check fails,
   * it redirects the user back to the "Tell a Friend" page with an appropriate error message.
   *
   * @return bool Returns false if the Antispam application is disabled; otherwise,
   *              returns void and handles validation/process internally.
   */
  public function execute()
  {
    if (!defined('CLICSHOPPING_APP_ANTISPAM_STATUS') || CLICSHOPPING_APP_ANTISPAM_STATUS == 'False') {
      return false;
    }

    if (isset($_GET['TellAFriend'], $_GET['Products'], $_GET['Process'])) {
      $error = false;

      $error_invisible = static::checkInvisibleAntispam();
      $error_numeric = static::checkNumericAntispam();

      if ($error_invisible === true || $error_numeric === true) {
        $error = true;
      }

      if ($error === true) {
        $id = HTML::sanitize($_GET['products_id']);
        $this->messageStack->add(CLICSHOPPING::getDef('text_error_antispam'), 'error');
        CLICSHOPPING::redirect(null, 'Products&TellAFriend&products_id=' . $id);
      }
    }
  }
}