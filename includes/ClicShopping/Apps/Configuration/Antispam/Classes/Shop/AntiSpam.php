<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Antispam\Classes\Shop;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;
use function defined;

class AntiSpam
{
  /**
   * Generates an anti-spam numeric confirmation string and stores a hashed value in the session.
   *
   * @return string Returns the anti-spam numeric confirmation string for display or verification purposes.
   */
  public static function getConfirmationNumericAntiSpam(): string
  {
    $random_number = rand(1, 200);

    $number = $random_number + 3;
    $antispam = ' (' . $random_number . ' + ' . CLICSHOPPING::getDef('text_antispam') . ') x 1';

    $_SESSION['createResponseAntiSpam'] = md5($number);

    return $antispam;
  }

  /**
   * Validates whether the provided numeric confirmation matches the anti-spam value stored in the session.
   *
   * @param mixed $antispan_confirmation The numeric confirmation to validate.
   * @return bool Returns true if the numeric confirmation is valid, otherwise false.
   */
  private static function checkNumeric($antispan_confirmation): bool
  {
    if (isset($_SESSION['createResponseAntiSpam'])) {
      if ($antispan_confirmation === $_SESSION['createResponseAntiSpam']) {
        $valid_antispan_confirmation = false;
      } else {
        $valid_antispan_confirmation = true;
      }
    } else {
      $valid_antispan_confirmation = true;
    }

    unset($_SESSION['createResponseAntiSpam']);

    return $valid_antispan_confirmation;
  }

  /**
   * Validates the numeric anti-spam input from a form submission.
   *
   * Checks if the 'antispam' field in the POST request contains the correct data
   * to prevent automated submissions.
   *
   * @return bool Returns true if the anti-spam validation fails, otherwise false.
   */
  public static function checkNumericAntiSpam(): bool
  {
    $error = false;
    if (isset($_POST['antispam'])) {
      $antispam = HTML::sanitize($_POST['antispam']);
      $result = md5($antispam);

      if (self::checkNumeric($result) === true) {
        $error = true;
      }
    } else {
      $error = true;
    }

    return $error;
  }
}