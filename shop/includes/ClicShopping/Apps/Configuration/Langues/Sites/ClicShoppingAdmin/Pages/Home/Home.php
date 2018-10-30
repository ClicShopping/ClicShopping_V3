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

  namespace ClicShopping\Apps\Configuration\Langues\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Configuration\Langues\Langues;

  class Home extends \ClicShopping\OM\PagesAbstract {
    public $app;

    protected function init() {
      $CLICSHOPPING_Langues = new Langues();
      Registry::set('Langues', $CLICSHOPPING_Langues);

      $this->app = $CLICSHOPPING_Langues;

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
