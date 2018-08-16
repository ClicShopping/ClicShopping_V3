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


  namespace ClicShopping\Apps\Catalog\Suppliers\Sites\ClicShoppingAdmin\Pages\Home\Actions\Suppliers;

  use ClicShopping\OM\Registry;
  use ClicShopping\Apps\Catalog\Suppliers\Classes\ClicShoppingAdmin\Status;

  class SetFlag extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;

    public function __construct() {
      $this->app = Registry::get('Suppliers');
    }

    public function execute() {

      Status::getSuppliersStatus($_GET['id'], $_GET['flag']);

      $this->app->redirect('Suppliers&' . $_GET['page'] . '&mID=' . $_GET['id']);
    }
  }