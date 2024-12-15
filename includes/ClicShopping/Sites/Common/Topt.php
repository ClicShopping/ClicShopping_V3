<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Sites\Common;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;
use RobThree\Auth\TwoFactorAuth;
/**
 * Class Topt
 * Provides methods to handle Two-Factor Authentication (TFA) operations such as generating secrets, verifying codes, and managing TFA-related data for administrators and customers.
 */
class Topt
{
  /**
   *
   * @return mixed Returns an instance of TwoFactorAuth from the Registry. If it does not exist, it initializes and stores it in the Registry.
   */
  public static function getTwoFactorAuth(): mixed
  {
    if (!Registry::exists('TwoFactorAuth')) {
      Registry::set('TwoFactorAuth', new TwoFactorAuth());
    }

    $CLICSHOPPING_TwoFactorAuth = Registry::get('TwoFactorAuth');

    return $CLICSHOPPING_TwoFactorAuth;
  }

  /**
   * Checks the authentication details for an administrator based on the provided username.
   *
   * @param string $username The username of the administrator whose authentication details are being checked.
   * @return string Returns the double authentication secret associated with the specified username.
   */
  public static function checkAuthAdmin(string $username): string
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qadmin = $CLICSHOPPING_Db->get('administrators', 'double_authentification_secret', ['user_name' => $username]);

    return $Qadmin->value('double_authentification_secret');
  }

  /**
   * Retrieves and generates a new Two-Factor Authentication (TFA) secret.
   *
   * @return string The newly created TFA secret.
   */
  public static function getTfaSecret(): string
  {
    $result = static::getTwoFactorAuth()->createSecret();

    return $result;
  }

  /**
   * Generates an image tag containing the QR Code for two-factor authentication.
   *
   * @param string $name The name or identifier associated with the account.
   * @param string $tfa_secret The secret key used for generating the QR Code.
   * @return string The HTML image tag with the embedded QR Code as a data URI.
   */
  public static function getImageTopt(string $name, string $tfa_secret): string
  {
     $result = '<img src="' . static::getTwoFactorAuth()->getQRCodeImageAsDataUri($name, $tfa_secret) . '">';

    return $result;
  }

  /**
   * Verifies the provided two-factor authentication (TFA) code against the given TFA secret.
   *
   * @param string $tfa_secret The secret key associated with the user's TFA setup.
   * @param string $tfaCode The TFA code provided by the user for verification.
   * @return bool True if the verification is successful, false otherwise.
   */
  public static function getVerifyAuth(string $tfa_secret, string $tfaCode): bool
  {
    $result = static::getTwoFactorAuth()->verifyCode($tfa_secret, $tfaCode);

    return $result;
  }

  /**
   * Checks the two-factor authentication login secret for a customer based on their email address.
   *
   * @param string $email The email address of the customer whose two-factor authentication secret is being retrieved.
   * @return string The two-factor authentication secret associated with the provided email address.
   */
  public static function checkToptloginCustomer(string $email): string
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qcheck = $CLICSHOPPING_Db->get('customers', 'double_authentification_secret', ['customers_email_address' => $email]);

    return $Qcheck->value('double_authentification_secret');
  }


  /**
   * Resets all admin-related session variables to log out the current administrator.
   *
   * @return void
   */
  public static function resetAllAdmin(): void
  {
    unset($_SESSION['username']);
    unset($_SESSION['password']);
    unset($_SESSION['user_secret']);
    unset($_SESSION['tfa_secret']);
    unset($_SESSION['adminAuth']);
  }

  /**
   * Resets all session variables related to the user authentication and session data.
   *
   * @return void
   */
  public static function resetAll(): void
  {
    unset($_SESSION['customer_id']);
    unset($_SESSION['password']);
    unset($_SESSION['email_address']);
    unset($_SESSION['tfa_secret']);
    unset($_SESSION['user_secret']);
  }
}