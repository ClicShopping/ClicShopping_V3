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

  namespace ClicShopping\Apps\Catalog\Products\Sites\ClicShoppingAdmin\Pages\Home\Actions\Products;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  class Update extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;
    protected $ID;
    protected $currentCategoryId;

    public function __construct(){
      $this->app = Registry::get('Products');

      if (isset($_GET['pID'])) {
        $this->ID = HTML::sanitize($_GET['pID']); // insert
      } elseif (isset($_POST['pID'])) {
        $this->ID = HTML::sanitize($_POST['pID']); // update
      }

      $current_category = HTML::sanitize($_POST['cPath']);
      $move_to_category_id = HTML::sanitize($_POST['move_to_category_id'][0]);

      if ($current_category != $move_to_category_id) {
        $this->currentCategoryId = $move_to_category_id;
      } else {
        $this->currentCategoryId = $current_category;
      }
    }

    public function execute() {
      $CLICSHOPPING_Hooks = Registry::get('Hooks');
      $CLICSHOPPING_ProductsAdmin = Registry::get('ProductsAdmin');

      $CLICSHOPPING_ProductsAdmin->save($this->ID, 'Update');

      $CLICSHOPPING_Hooks->call('Products','Update');

      $this->app->redirect('Products&cPath=' . $this->currentCategoryId . '&pID=' . $this->ID);
    }
  }