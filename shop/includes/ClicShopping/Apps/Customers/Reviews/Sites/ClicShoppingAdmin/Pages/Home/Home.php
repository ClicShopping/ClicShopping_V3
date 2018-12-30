<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\Apps\Customers\Reviews\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Customers\Reviews\Reviews;

  class Home extends \ClicShopping\OM\PagesAbstract {
    public $app;

    protected function init() {
      $CLICSHOPPING_Reviews = new Reviews();
      Registry::set('Reviews', $CLICSHOPPING_Reviews);

      $this->app = Registry::get('Reviews');

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
