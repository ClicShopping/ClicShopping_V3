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


  namespace ClicShopping\Apps\Catalog\Manufacturers\Sites\ClicShoppingAdmin\Pages\Home\Actions\Manufacturers;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Catalog\Manufacturers\Classes\ClicShoppingAdmin\Status;

  class SetFlag extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;

    public function __construct() {
      $this->app = Registry::get('Manufacturers');
    }

    public function execute() {

      Status::getManufacturersStatus($_GET['id'], $_GET['flag']);

      $this->app->redirect('Manufacturers&' . $_GET['page'] . '&mID=' . $_GET['id']);
    }
  }