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

  namespace ClicShopping\Apps\Marketing\Favorites\Module\Hooks\ClicShoppingAdmin\CustomersGroup;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Marketing\Favorites\Favorites as FavoritesApp;

  class Delete implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;

    public function __construct()
    {
      if (!Registry::exists('Favorites')) {
        Registry::set('Favorites', new FavoritesApp());
      }

      $this->app = Registry::get('Favorites');
    }

    /**
     * @param int $group_id
     */
    private function delete(int $group_id) :void
    {
      $QProductsFavoritesCustomersId = $this->app->db->prepare('select count(customers_group_id) as count
                                                                 from :table_products_favorites
                                                                 where customers_group_id = :customers_group_id
                                                               ');
      $QProductsFavoritesCustomersId->bindInt(':customers_group_id', (int)$group_id);
      $QProductsFavoritesCustomersId->execute();

      if ($QProductsFavoritesCustomersId->valueInt('count') > 0) {
        $Qdelete = $this->app->db->prepare('delete
                                            from :table_products_favorites
                                            where customers_group_id = :customers_group_id
                                          ');
        $Qdelete->bindInt(':customers_group_id', (int)$group_id);
        $Qdelete->execute();
      }
    }

    public function execute()
    {
      if (isset($_GET['Delete'])) {
        $id = HTML::sanitize($_GET['cID']);
        $this->delete($id);
      }
    }
  }