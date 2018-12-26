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

  namespace ClicShopping\Apps\Marketing\Featured\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Marketing\Featured\Featured as FeaturedApp;

  class Save implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;

    public function __construct()   {
      if (!Registry::exists('Featured')) {
        Registry::set('Featured', new FeaturedApp());
      }

      $this->app = Registry::get('Featured');
    }

    private function saveProductsFeatured($id) {
      $CLICSHOPPING_Db = Registry::get('Db');

      if (!empty($_POST['products_featured'])) {
        $CLICSHOPPING_Db->save('products_featured', ['products_id' => (int)$id,
                                              'products_featured_date_added' => 'now()',
                                              'status' => 1,
                                              'customers_group_id' => 0
                                            ]
                         );
      }
    }

    private function save($id) {
      $this->saveProductsFeatured($id);
    }

    public function execute() {
      $id = HTML::sanitize($_GET['pID']);
      $this->save($id);
    }
  }