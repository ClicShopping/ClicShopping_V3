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

  namespace ClicShopping\Service\Shop;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Customers\Reviews\Classes\Shop\ReviewsClass as NewReviews;

  class Reviews implements \ClicShopping\OM\ServiceInterface {

    public static function start() {

      if (is_file(CLICSHOPPING_BASE_DIR . 'Apps/Customers/Reviews/Classes/Shop/ReviewsClass.php')) {
        Registry::set('Reviews', new NewReviews());

        return true;
      } else {
        return false;
      }
    }

    public static function stop() {
      return true;
    }
  }
