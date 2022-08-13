<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */


  namespace ClicShopping\Apps\Catalog\Manufacturers\Sites\ClicShoppingAdmin\Pages\Home\Actions\Manufacturers;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Catalog\Manufacturers\Classes\ClicShoppingAdmin\Status;

  class SetFlag extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected mixed $app;

    public function __construct()
    {
      $this->app = Registry::get('Manufacturers');
    }

    public function execute()
    {

      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

      if (isset($_GET['id'], $_GET['flag'])) {
        Status::getManufacturersStatus($_GET['id'], $_GET['flag']);

        $this->app->redirect('Manufacturers&page=' . $page . '&mID=' . (int)$_GET['id']);
      } else {
        $this->app->redirect('Manufacturers&page=' . $page);
      }
    }
  }