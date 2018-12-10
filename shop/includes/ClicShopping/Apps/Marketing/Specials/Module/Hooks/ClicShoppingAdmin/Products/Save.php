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

  namespace ClicShopping\Apps\Marketing\Specials\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Marketing\Specials\Specials as SpecialsApp;

  class Save implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;

    public function __construct()   {
      if (!Registry::exists('Specials')) {
        Registry::set('Specials', new SpecialsApp());
      }

      $this->app = Registry::get('Specials');
    }

    private function saveProductsSpecials($id) {
      if (!empty($_POST['products_specials'])) {
        if (isset($_POST['percentage_products_specials'])) {
          if (substr($_POST['percentage_products_specials'], -1) == '%') {
            $specials_price = ($_POST['products_price'] - (($_POST['percentage_products_specials'] / 100) *  $_POST['products_price']));
          } else {
            $specials_price = $_POST['products_price'] - $_POST['percentage_products_specials'];
          }

          if (is_float($specials_price)) {
            $this->app->db->save('specials', ['products_id' => (int)$id,
                                              'specials_new_products_price' => (float)$specials_price,
                                              'specials_date_added' => 'now()',
                                              'status' => 1,
                                              'customers_group_id' => 0
                                             ]
                          );
          } // end is_numeric
        } // $_POST['percentage_products_specials']
      } // $_POST['products_specials']
    }

    private function save($id) {
      $this->saveProductsSpecials($id);
    }

    public function execute() {
      $id = HTML::sanitize($_GET['pID']);
      $this->save($id);
    }
  }