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


  namespace ClicShopping\Apps\Tools\AdministratorMenu\Sites\ClicShoppingAdmin\Pages\Home\Actions\AdministratorMenu;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  use ClicShopping\Apps\Tools\AdministratorMenu\Classes\ClicShoppingAdmin\AdministratorMenu;

  class DeleteCategoryConfirm extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;

    public function __construct() {
      $this->app = Registry::get('AdministratorMenu');

      $this->ID = HTML::sanitize($_GET['id']);
      $this->cPath = $_GET['cPath'];
    }

    public function execute() {

      if (isset($this->ID)) {
        $categories = AdministratorMenu::getAdministratorMenuCategoryTree($this->ID, '', '0', '', true);

        for ($i=0, $n=count($categories); $i<$n; $i++) {
          AdministratorMenu::getRemoveAdministratorMenuCategory($categories[$i]['id']);
        }
      }

      Cache::clear('menu-administrator');

      $this->app->redirect('AdministratorMenu&cPath=' . $this->cPath);
    }
  }