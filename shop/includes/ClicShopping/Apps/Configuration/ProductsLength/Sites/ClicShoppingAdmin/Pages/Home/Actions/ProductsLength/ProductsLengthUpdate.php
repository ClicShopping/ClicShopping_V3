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

  namespace ClicShopping\Apps\Configuration\ProductsLength\Sites\ClicShoppingAdmin\Pages\Home\Actions\ProductsLength;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Cache;

  class ProductsLengthUpdate extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {
      $this->app = Registry::get('ProductsLength');
    }

    public function execute()
    {
      $CLICSHOPPING_Language = Registry::get('Language');

      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;;
      $languages = $CLICSHOPPING_Language->getLanguages();

      $products_length_class_key = HTML::sanitize($_POST['products_length_class_key']);
      $products_length_class_id = HTML::sanitize($_POST['products_length_class_id']);

      for ($i = 0, $n = count($languages); $i < $n; $i++) {
        $products_length_class_title_array = HTML::sanitize($_POST['products_length_class_title']);
        $language_id = $languages[$i]['id'];

        $products_length_class_title_array = HTML::sanitize($products_length_class_title_array[$language_id]);

        $sql_data_array = ['products_length_class_title' => $products_length_class_title_array,
          'products_length_class_key' => $products_length_class_key
        ];

        $this->app->db->save('products_length_classes', $sql_data_array, ['products_length_class_id' => (int)$products_length_class_id,
            'language_id' => (int)$language_id
          ]
        );
      }

      Cache::clear('products_length-classes');
      Cache::clear('products_length-rules');

      $this->app->redirect('ProductsLength&page=' . $page);
    }
  }