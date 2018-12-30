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

  namespace ClicShopping\Apps\Communication\Newsletter\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Communication\Newsletter\Newsletter;

  class Home extends \ClicShopping\OM\PagesAbstract {
    public $app;

    protected function init() {
      $CLICSHOPPING_Newsletter = new Newsletter();
      Registry::set('Newsletter', $CLICSHOPPING_Newsletter);

      $this->app = Registry::get('Newsletter');

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
