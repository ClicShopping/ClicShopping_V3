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

  namespace ClicShopping\Apps\Configuration\ProductsQuantityUnit\Sites\ClicShoppingAdmin\Pages\Home\Actions\ProductsQuantityUnit;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class Update extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {
      $this->app = Registry::get('ProductsQuantityUnit');
    }

    public function execute()
    {
      $CLICSHOPPING_Language = Registry::get('Language');

      if (isset($_GET['oID'])) $products_quantity_unit_id = HTML::sanitize($_GET['oID']);

      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? HTML::sanitize($_GET['page']) : 1;
      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = count($languages); $i < $n; $i++) {
        $products_quantity_unit_title_array = $_POST['products_quantity_unit_title'];
        $language_id = $languages[$i]['id'];

        $sql_data_array = ['products_quantity_unit_title' => HTML::sanitize($products_quantity_unit_title_array[$language_id])];

        $this->app->db->save('products_quantity_unit', $sql_data_array, ['products_quantity_unit_id' => (int)$products_quantity_unit_id,
            'language_id' => (int)$language_id
          ]
        );
      }

      if (isset($_POST['default'])) {
        $this->app->db->save('configuration', [
          'configuration_value' => $products_quantity_unit_id
        ], [
            'configuration_key' => 'DEFAULT_PRODUCTS_QUANTITY_UNIT_STATUS_ID'
          ]
        );
      }

      Cache::clear('configuration');

      $this->app->redirect('ProductsQuantityUnit&page=' .$page . '&oID=' . $products_quantity_unit_id);
    }
  }