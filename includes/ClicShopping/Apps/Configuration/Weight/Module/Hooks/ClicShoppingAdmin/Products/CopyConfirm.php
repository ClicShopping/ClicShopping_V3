<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\Weight\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Configuration\Weight\Weight as WeightApp;

  class CopyConfirm implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;

    public function __construct()
    {
      if (!Registry::exists('Weight')) {
        Registry::set('Weight', new WeightApp());
      }

      $this->app = Registry::get('Weight');
    }

    public function execute()
    {
      if (!\defined('CLICSHOPPING_APP_WEIGHT_WE_STATUS') || CLICSHOPPING_APP_WEIGHT_WE_STATUS == 'False') {
        return false;
      }

      if (isset($_GET['products_id'])) {
        $current_products_id = HTML::sanitize($_POST['products_id']);

        if (isset($current_products_id, $_GET['CopyConfirm'])) {
          $weight = $this->app->db->prepare('select products_weight_class_id
                                             from :table_products
                                             where products_id = :products_id
                                            ');
          $weight->bindInt(':products_id', $current_products_id);
          $weight->execute();

          $products_weight_class_id = $weight->valueInt('products_weight_class_id');

          $Qproducts = $this->app->db->prepare('select products_id 
                                                from :table_products                                            
                                                order by products_id desc
                                                limit 1 
                                               ');
          $Qproducts->execute();

          $id = $Qproducts->valueInt('products_id');

          $sql_data_array = ['products_weight_class_id' => (int)$products_weight_class_id];

          $this->app->db->save('products', $sql_data_array, ['products_id' => (int)$id]);
        }
      }
    }
  }