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

  namespace ClicShopping\Apps\Customers\Groups\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Customers\Groups\Groups;

  class Home extends \ClicShopping\OM\PagesAbstract
  {
    public $app;

    protected function init()
    {
      $CLICSHOPPING_Groups = new Groups();
      Registry::set('Groups', $CLICSHOPPING_Groups);

      $this->app = Registry::get('Groups');

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
