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

  class ProductsLengthInsert extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {
      $this->app = Registry::get('ProductsLength');
    }

    public function execute()
    {
      $CLICSHOPPING_Language = Registry::get('Language');

      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;
      $languages = $CLICSHOPPING_Language->getLanguages();

      $QlastId = $this->app->db->prepare('select products_length_class_id
                                            from :table_products_length_classes
                                            order by products_length_class_id desc
                                            limit 1
                                           ');
      $QlastId->execute();

      $products_length_class_id = $QlastId->valueInt('products_length_class_id') + 1;
      $products_length_class_key = HTML::sanitize($_POST['products_length_class_key']);

      for ($i = 0, $n = count($languages); $i < $n; $i++) {
        $products_length_class_title_array = HTML::sanitize($_POST['products_length_class_title']);
        $language_id = $languages[$i]['id'];

        $products_length_class_title_array = HTML::sanitize($products_length_class_title_array[$language_id]);

        $sql_data_array = ['products_length_class_title' => $products_length_class_title_array];

        $insert_sql_data = ['products_length_class_key' => $products_length_class_key,
          'products_length_class_id' => (int)$products_length_class_id,
          'language_id' => (int)$languages[$i]['id']
        ];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $this->app->db->save('products_length_classes', $sql_data_array);
      }

      Cache::clear('products_length-classes');
      Cache::clear('products_length-rules');

      $this->app->redirect('ProductsLength&page=' . $page);
    }
  }