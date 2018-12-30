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

  namespace ClicShopping\Apps\Marketing\BannerManager\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Marketing\BannerManager\BannerManager;

  class Home extends \ClicShopping\OM\PagesAbstract {
    public $app;

    protected function init() {
      $CLICSHOPPING_BannerManager = new BannerManager();
      Registry::set('BannerManager', $CLICSHOPPING_BannerManager);

      $this->app = $CLICSHOPPING_BannerManager;

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
