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

  namespace ClicShopping\Apps\Marketing\Favorites\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Marketing\Favorites\Favorites as FavoritesApp;

  class Save implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;

    public function __construct()
    {
      if (!Registry::exists('Favorites')) {
        Registry::set('Favorites', new FavoritesApp());
      }

      $this->app = Registry::get('Favorites');
    }

    private function saveProductsFavorites($id)
    {
      if (!empty($_POST['products_favorites'])) {
        $this->app->db->save('products_favorites', [
          'products_id' => (int)$id,
          'products_favorites_date_added' => 'now()',
          'status' => 1,
          'customers_group_id' => 0
          ]
        );
      }
    }

    private function save($id)
    {
      $this->saveProductsFavorites($id);
    }

    public function execute()
    {
      if (isset($_GET['pID'])) {
        $id = HTML::sanitize($_GET['pID']);
        $this->save($id);
      }
    }
  }