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

  namespace ClicShopping\Apps\Products\QuantityDiscount\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Products\QuantityDiscount\QuantityDiscount as QuantityDiscountApp;

  class RemoveProduct implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;

    public function __construct()   {
      if (!Registry::exists('QuantityDiscount')) {
        Registry::set('QuantityDiscount', new QuantityDiscountApp());
      }

      $this->app = Registry::get('QuantityDiscount');
    }

    private function removeReviews($id) {
      $Qreviews = $this->app->db->get('reviews', 'reviews_id', ['products_id' => (int)$id]);

      $this->app->db->delete('reviews', ['products_id' => (int)$id]);

      while ($Qreviews->fetch()) {
        $this->app->db->delete('reviews_description', ['reviews_id' => $Qreviews->valueInt('reviews_id')]);
      }
    }

    public function execute() {
      $id = HTML::sanitize($_POST['remove_id']);
      $this->removeReviews($id);
    }
  }