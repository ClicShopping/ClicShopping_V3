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

  namespace ClicShopping\Apps\Marketing\Favorites\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Marketing\Favorites\Favorites as FavoritesApp;

  class RemoveProduct implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;

    public function __construct()   {
      if (!Registry::exists('Favorites')) {
        Registry::set('Favorites', new FavoritesApp());
      }

      $this->app = Registry::get('Favorites');
    }

    private function removeMarketing($id) {
      if (!empty($_POST['products_favorites'])) {
        $this->app->db->delete('products_favorites', ['products_id' => (int)$id]);
      }
    }


    public function execute() {
      $id = HTML::sanitize($_POST['pID']);
      $this->removeMarketing($id);
    }
  }