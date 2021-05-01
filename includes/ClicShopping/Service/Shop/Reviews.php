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

  use ClicShopping\Apps\Customers\Reviews\Classes\Shop\ReviewsClass as NewReviews;

  class Reviews implements \ClicShopping\OM\ServiceInterface
  {
    public static function start(): bool
    {
      if (is_file(CLICSHOPPING::BASE_DIR . 'Apps/Customers/Reviews/Classes/Shop/ReviewsClass.php')) {
        Registry::set('Reviews', new NewReviews());

        return true;
      } else {
        return false;
      }
    }

    public static function stop(): bool
    {
      return true;
    }
  }
