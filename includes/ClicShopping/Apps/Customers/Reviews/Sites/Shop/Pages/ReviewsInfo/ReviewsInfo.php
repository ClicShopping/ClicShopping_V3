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

  namespace ClicShopping\Apps\Customers\Reviews\Sites\Shop\Pages\ReviewsInfo;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Customers\Reviews\Reviews as ReviewsApp;

  class ReviewsInfo extends \ClicShopping\OM\PagesAbstract
  {
    public $app;

    protected function init()
    {

      if (!Registry::exists('ReviewsApp')) {
        Registry::set('ReviewsApp', new ReviewsApp());
      }

      $CLICSHOPPING_Reviews = Registry::get('ReviewsApp');

      $CLICSHOPPING_Reviews->loadDefinitions('Sites/Shop/main');
    }
  }
