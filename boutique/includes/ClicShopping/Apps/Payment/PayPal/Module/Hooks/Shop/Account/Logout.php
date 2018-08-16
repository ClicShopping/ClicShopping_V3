<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4 
 *
 *
 */

  namespace ClicShopping\Apps\Payment\PayPal\Module\Hooks\Shop\Account;

  class Logout implements \ClicShopping\OM\Modules\HooksInterface  {
    public function execute() {
      if (isset($_SESSION['paypal_login_access_token'])) {
          unset($_SESSION['paypal_login_access_token']);
      }
    }
  }
