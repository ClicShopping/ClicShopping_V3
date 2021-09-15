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

  namespace ClicShopping\Apps\Payment\Stripe\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Payment\Stripe\Stripe;

  class Home extends \ClicShopping\OM\PagesAbstract {
    public mixed $app;

    protected function init() {
      $CLICSHOPPING_Stripe = new Stripe();
      Registry::set('Stripe', $CLICSHOPPING_Stripe);

      $this->app = $CLICSHOPPING_Stripe;

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
