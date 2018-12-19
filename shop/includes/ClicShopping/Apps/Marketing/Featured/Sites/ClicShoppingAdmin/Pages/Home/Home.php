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

  namespace ClicShopping\Apps\Marketing\Featured\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Marketing\Featured\Featured;

  class Home extends \ClicShopping\OM\PagesAbstract {
    public $app;

    protected function init() {
      $CLICSHOPPING_Featured = new Featured();
      Registry::set('Featured', $CLICSHOPPING_Featured);

      $this->app = $CLICSHOPPING_Featured;

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
