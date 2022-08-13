<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Tools\AdministratorMenu\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Tools\AdministratorMenu\AdministratorMenu;

  class Home extends \ClicShopping\OM\PagesAbstract
  {
    public mixed $app;

    protected function init()
    {
      $CLICSHOPPING_AdministratorMenu = new AdministratorMenu();
      Registry::set('AdministratorMenu', $CLICSHOPPING_AdministratorMenu);

      $this->app = $CLICSHOPPING_AdministratorMenu;

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
