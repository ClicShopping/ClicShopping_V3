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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class ClassDeleteConfirm extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {
      $this->app = Registry::get('ProductsLength');
    }

    public function execute()
    {

      $products_length_class_from_id = HTML::sanitize($_GET['wID']);
      $products_length_class_to_id = HTML::sanitize($_GET['tID']);

      $this->app->db->delete('products_length_classes_rules', ['products_length_class_from_id' => (int)$products_length_class_from_id,
          'products_length_class_from_id' => (int)$products_length_class_to_id,
        ]
      );

      $this->app->db->delete('products_length_classes', ['products_length_class_id' => (int)$products_length_class_from_id]);

      Cache::clear('products_length-classes');
      Cache::clear('products_length-rules');

      $this->app->redirect('ProductsLength&' . (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : ''));
    }
  }