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

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Configuration\Weight\Classes\Shop\Weight as WeightShop;

  class Weight implements \ClicShopping\OM\ServiceInterface
  {

    public static function start()
    {

      if (is_file(CLICSHOPPING::BASE_DIR . 'Apps/Configuration/Weight/Classes/Shop/Weight.php')) {
        Registry::set('Weight', new WeightShop());

        return true;
      } else {
        return false;
      }
    }

    public static function stop()
    {
      return true;
    }
  }
