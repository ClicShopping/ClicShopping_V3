<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\ProductsAttributes\Sites\ClicShoppingAdmin\Pages\Home\Actions\ProductsAttributes;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class CloneAttributes extends \ClicShopping\OM\PagesActionsAbstract
{
  public mixed $app;

  public function __construct()
  {
    $this->app = Registry::get('ProductsAttributes');
  }

  public function execute()
  {
    $CLICSHOPPING_Hooks = Registry::get('Hooks');

    $CLICSHOPPING_Hooks->call('CloneAttributes', 'PreAction');

    $multi_clone_products_id_to = HTML::sanitize($_POST['clone_products_id_to']);

    if (\is_array($multi_clone_products_id_to)) {
      for ($i = 0, $iMax = \count($multi_clone_products_id_to); $i < $iMax; $i++) {
        $clone_product_id_from = HTML::sanitize($_POST['clone_products_id_from']);
        $clone_product_id_to = $multi_clone_products_id_to[$i];

        $Qdelete = $this->app->db->prepare('delete
                                              from :table_products_attributes
                                              where products_id = :products_id
                                              ');
        $Qdelete->bindInt(':products_id', $clone_product_id_to);

        $Qdelete->execute();

        $Qattributes = $this->app->db->prepare('select products_id,
                                                         options_id,
                                                         options_values_id,
                                                         options_values_price,
                                                         price_prefix,
                                                         products_options_sort_order,
                                                         customers_group_id
                                                   from :table_products_attributes
                                                   where products_id = :products_id
                                                  ');
        $Qattributes->bindInt(':products_id', $clone_product_id_from);
        $Qattributes->execute();

        while ($Qattributes->fetch()) {
          $sql_array = [
            'products_id' => (int)$clone_product_id_to,
            'options_id' => (int)$Qattributes->valueInt('options_id'),
            'options_values_id' => (int)$Qattributes->valueInt('options_values_id'),
            'options_values_price' => (float)$Qattributes->valueDecimal('options_values_price'),
            'price_prefix' => $Qattributes->value('price_prefix'),
            'products_options_sort_order' => (int)$Qattributes->valueInt('products_options_sort_order'),
            'products_attributes_reference' => '',
            'customers_group_id' => (int)$Qattributes->valueInt('customers_group_id'),
          ];

          $this->app->db->save('products_attributes', [$sql_array]);
        }
      }
      $CLICSHOPPING_Hooks->call('CloneAttributes', 'Save');
    }

    $this->app->redirect('ProductsAttributes#tab3');
  }
}