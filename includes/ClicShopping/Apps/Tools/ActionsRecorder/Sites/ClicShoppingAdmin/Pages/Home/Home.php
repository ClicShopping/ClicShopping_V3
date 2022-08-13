<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Tools\ActionsRecorder\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Tools\ActionsRecorder\ActionsRecorder;

  class Home extends \ClicShopping\OM\PagesAbstract
  {
    public mixed $app;

    protected function init()
    {
      $CLICSHOPPING_ActionsRecorder = new ActionsRecorder();
      Registry::set('ActionsRecorder', $CLICSHOPPING_ActionsRecorder);

      $this->app = $CLICSHOPPING_ActionsRecorder;

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
