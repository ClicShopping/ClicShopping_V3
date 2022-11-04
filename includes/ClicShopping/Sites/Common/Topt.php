<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Sites\Common;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;


  use RobThree\Auth\TwoFactorAuth;

  class Topt
  {
    public function __construct() {
    }

    /**
     * @return mixed
     */
    public static function getTwoFactorAuth() :mixed
    {
      Registry::set('TwoFactorAuth', new TwoFactorAuth());
      $CLICSHOPPING_TwoFactorAuth = Registry::get('TwoFactorAuth');

      return $CLICSHOPPING_TwoFactorAuth;
    }

    /**
     * @param string $username
     * @return string
     */
    public static function checkAuthAdmin(string $username) :string
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qadmin = $CLICSHOPPING_Db->get('administrators',  'double_authentification_secret', ['user_name' => $username]);

      return $Qadmin->value('double_authentification_secret');
    }

    /**
     * @return string
     */
    public static function getTfaSecret(): string
    {
      $result  = static::getTwoFactorAuth()->createSecret();

      return $result;
    }

    /**
     * @param string $tfa_secret
     * @param string $name
     * @return string
     */
    public static function getImageTopt(string $name, string $tfa_secret) :string
    {
      if (CLICSHOPPING::getSite() === 'ClicShoppingAdmin') {
        $result = '<img src="' . static::getTwoFactorAuth()->getQRCodeImageAsDataUri('ClicShoppingAdmin', $tfa_secret) . '">';
       } else {
        $result = '<img src="' . static::getTwoFactorAuth()->getQRCodeImageAsDataUri($name, $tfa_secret) . '">';
      }

      return $result;
    }

    /**
     * @param string $tfa_secret
     * @param string $tfaCode
     * @return bool
     */
    public static function getVerifyAuth(string $tfa_secret, string $tfaCode) :bool
    {
      $result = static::getTwoFactorAuth()->verifyCode($tfa_secret, $tfaCode);

      return $result;
    }


    /**
     * @return void
     */
    public static function resetAll() :void
    {
      unset($_SESSION['customer_id']);
      unset($_SESSION['password']);
      unset($_SESSION['email_address']);
      unset($_SESSION['tfa_secret']);
      unset($_SESSION['user_secret']);
    }

    /**
     * @param string $email
     * @return string
     */
    public static function checkToptloginCustomer(string $email): string
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->get('customers', 'double_authentification_secret', ['customers_email_address' => $email]);

      return $Qcheck->value('double_authentification_secret');
    }

  }