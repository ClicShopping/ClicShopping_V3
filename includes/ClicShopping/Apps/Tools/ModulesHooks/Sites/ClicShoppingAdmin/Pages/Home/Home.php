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

  namespace ClicShopping\Apps\Tools\ModulesHooks\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Tools\ModulesHooks\ModulesHooks;

  class Home extends \ClicShopping\OM\PagesAbstract
  {
    public $app;

    protected function init()
    {
      $CLICSHOPPING_ModulesHooks = new ModulesHooks();
      Registry::set('ModulesHooks', $CLICSHOPPING_ModulesHooks);

      $this->app = Registry::get('ModulesHooks');

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
