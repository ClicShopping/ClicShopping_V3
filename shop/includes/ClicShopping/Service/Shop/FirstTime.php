<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Service\Shop;

  class FirstTime implements \ClicShopping\OM\ServiceInterface
  {

    public static function start()
    {

      if (WEBSITE_MODULE_INSTALLED == 0) {
        echo TEXT_INSTALL;
        echo '   <div class="text-md-center;"><br /><a href="https://www.clicshopping.org/marketplace" target="_blank"><img src="images/logo_clicshopping.png" border="0" height="100" width="100" alt="Market Place"><br />Go to Market Place</a></div>';
        echo '   <div class="text-md-center" style="font-size: 10px;padding-top:10px;">ClicShopping(TM) est une marque (trademark) déposée par Loïc Richard.</div>';
        exit;
      }
    }

    public static function stop()
    {
      return true;
    }
  }
