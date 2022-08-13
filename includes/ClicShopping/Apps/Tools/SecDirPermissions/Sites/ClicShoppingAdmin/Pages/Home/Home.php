<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Tools\SecDirPermissions\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Tools\SecDirPermissions\SecDirPermissions;

  class Home extends \ClicShopping\OM\PagesAbstract
  {
    public mixed $app;

    protected function init()
    {
      $CLICSHOPPING_SecDirPermissions = new SecDirPermissions();
      Registry::set('SecDirPermissions', $CLICSHOPPING_SecDirPermissions);

      $this->app = Registry::get('SecDirPermissions');

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
