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

  namespace ClicShopping\Apps\Tools\DataBaseTables\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Tools\DataBaseTables\DataBaseTables;

  class Home extends \ClicShopping\OM\PagesAbstract {
    public $app;

    protected function init() {
      $CLICSHOPPING_DataBaseTables = new DataBaseTables();
      Registry::set('DataBaseTables', $CLICSHOPPING_DataBaseTables);

      $this->app = Registry::get('DataBaseTables');

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
