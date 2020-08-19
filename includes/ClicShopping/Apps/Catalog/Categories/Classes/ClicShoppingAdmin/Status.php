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

  namespace ClicShopping\Apps\Catalog\Categories\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Catalog\Categories\Classes\Shop\CategoryTree;

  class Status
  {
    protected int $status;
    protected int $categories_id;

    /**
     * Categories Status - Sets the status of a categorie
     *
     * @param int $categories_id
     * @param int $status
     * @return string status on or off
     *
     */
    public static function getCategoriesStatus(int $categories_id, int $status)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if (Registry::exists('CategoryTree')) {
        $CLICSHOPPING_CategoryTree = Registry::get('CategoryTree');
      } else {
        $CLICSHOPPING_CategoryTree = new CategoryTree();
        Registry::set('CategoryTree', $CLICSHOPPING_CategoryTree);
      }

      if ($status == 1) {
        $data = ['id' => $categories_id];

        foreach (array_merge(array($data['id']), $CLICSHOPPING_CategoryTree->getChildren($data['id'])) as $c) {
          $sql_array = ['status' => 1,
            'last_modified' => 'now()'
          ];

          $update_array = ['categories_id' => (int)$c];

          $CLICSHOPPING_Db->save('categories', $sql_array, $update_array);
        }

      } elseif ($status == 0) {
        $data = ['id' => $categories_id];

        foreach (array_merge(array($data['id']), $CLICSHOPPING_CategoryTree->getChildren($data['id'])) as $c) {
          $sql_array = [
            'status' => 0,
            'last_modified' => 'now()'
          ];

          $update_array = ['categories_id' => (int)$c];

          $CLICSHOPPING_Db->save('categories', $sql_array, $update_array);
        }
      } else {
        return -1;
      }
    }
  }