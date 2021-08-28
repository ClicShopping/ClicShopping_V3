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


  namespace ClicShopping\Apps\Catalog\Categories\Sites\ClicShoppingAdmin\Pages\Home\Actions\Categories;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Cache;

  class MoveConfirm extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected mixed $app;
    protected $ID;
    protected $cPath;
    protected $moveToCategoryID;
    protected $categoriesAdmin;

    public function __construct()
    {
      $this->app = Registry::get('Categories');

      $this->categoriesAdmin = Registry::get('CategoriesAdmin');

      if (isset($_GET['categories_id'])) {
        $this->Id = HTML::sanitize($_GET['categories_id']); // insert
      } elseif (isset($_POST['categories_id'])) {
        $this->Id = HTML::sanitize($_POST['categories_id']); // update
      }

      $this->moveToCategoryID = HTML::sanitize($_POST['move_to_category_id']);

      if (isset($_GET['cPath'])) {
        $this->cPath = HTML::sanitize($_GET['cPath']);
      } else {
        $this->cPath = 0;
      }
    }

    public function execute()
    {
      $CLICSHOPPING_Hooks = Registry::get('Hooks');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      if (isset($this->Id) && ($this->Id != $this->moveToCategoryID)) {
        $categories_id = HTML::sanitize($this->Id);
        $new_parent_id = HTML::sanitize($this->moveToCategoryID);

        $path = explode('_', $this->categoriesAdmin->getGeneratedCategoryPathIds($new_parent_id));

        if (\in_array($this->Id, $path)) {
          $CLICSHOPPING_MessageStack->add($this->app->getDef('error_cannot_move_directory_to_parent'), 'error');

          $this->app->redirect('Categories&cPath=' . $this->cPath . '&cID=' . $categories_id);
        } else {
          $sql_array = [
            'parent_id' => (int)$new_parent_id,
            'last_modified' => 'now()'
          ];

          $insert_array = [
            'categories_id' => (int)$categories_id
          ];

          $this->app->db->save('categories', $sql_array, $insert_array);

          Cache::clear('categories');
          Cache::clear('products-also_purchased');
          Cache::clear('products_related');
          Cache::clear('products_cross_sell');
          Cache::clear('upcoming');

          $CLICSHOPPING_Hooks->call('Categories', 'Insert');

          $this->app->redirect('Categories&cPath=' . $new_parent_id);
        }
      }
    }
  }