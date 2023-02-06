<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\Antispam\Module\Hooks\Shop\Account\Create;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Configuration\Antispam\Antispam as AntispamApp;
  use ClicShopping\Apps\Configuration\Antispam\Classes\Shop\AntiSpam;

  class PreAction implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;
    public mixed $messageStack;

    public function __construct()
    {
      if (!Registry::exists('Antispam')) {
        Registry::set('Antispam', new AntispamApp());
      }

      $this->app = Registry::get('Antispam');
      $this->messageStack = Registry::get('MessageStack');
    }

    /**
     * @return bool
     */
    private static function checkInvisibleAntispam() :bool
    {
      $error = false;

      if (!\defined('CLICSHOPPING_APP_ANTISPAM_IN_STATUS') || CLICSHOPPING_APP_ANTISPAM_IN_STATUS == 'False') {
        $error = false;
      } elseif (!\defined('CLICSHOPPING_APP_ANTISPAM_IN_CREATE_ACCOUNT') || CLICSHOPPING_APP_ANTISPAM_IN_STATUS == 'False') {
        $error = false;
      } elseif (!isset($_POST['invisible_clicshopping'])) {
        $error = true;
      }

      return $error;
    }

    /**
     * @return bool
     */
    private static function checkNumericAntispam() :bool
    {
      $error = false;

      if (!\defined('CLICSHOPPING_APP_ANTISPAM_AM_STATUS') || CLICSHOPPING_APP_ANTISPAM_AM_STATUS == 'False') {
        $error = false;
      } elseif (!\defined('CLICSHOPPING_APP_ANTISPAM_AM_CREATE_ACCOUNT') || CLICSHOPPING_APP_ANTISPAM_AM_STATUS == 'False') {
        $error = false;
      } else {
        $error = AntiSpam::checkNumericAntiSpam();
      }

      return $error;
    }

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
            CLICSHOPPING::redirect(null, 'Account&Create');
        }
      }
    }
  }