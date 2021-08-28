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

  namespace ClicShopping\Apps\Configuration\ProductsQuantityUnit\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;

  class ProductsQuantityUnitAdmin
  {
    protected mixed $app;

    public function __construct()
    {
      $this->app = Registry::get('ProductsQuantityUnit');
    }

    /**
     * productsQuantityUnitDropDown
     *
     * @param string
     * @return string $products_quantity_unit_array, elements of product quantity unit
     *
     */

    public function productsQuantityUnitDropDown(): array
    {
      $CLICSHOPPING_Language = Registry::get('Language');

      $products_quantity_unit_array = array(array('id' => '',
        'text' => $this->app->getDef('text_none'))
      );

      $QproductsQuantityUnit = $this->app->db->prepare('select products_quantity_unit_id,
                                                               products_quantity_unit_title
                                                       from :table_products_quantity_unit
                                                       where language_id = :language_id
                                                       order by products_quantity_unit_id
                                                      ');
      $QproductsQuantityUnit->bindInt(':language_id', $CLICSHOPPING_Language->getId());
      $QproductsQuantityUnit->execute();

      while ($QproductsQuantityUnit->fetch() !== false) {
        $products_quantity_unit_array[] = ['id' => $QproductsQuantityUnit->valueInt('products_quantity_unit_id'),
          'text' => $QproductsQuantityUnit->value('products_quantity_unit_title')
        ];
      }

      return $products_quantity_unit_array;
    }

    public function getProductsQuantityUnitTitle(): string
    {
      $CLICSHOPPING_Language = Registry::get('Language');
      $QproductQtyUnit = $this->app->db->prepare('select p.products_quantity_unit_id,
                                                        pqt.products_quantity_unit_title
                                                 from :table_products p,
                                                      :table_products_quantity_unit pqt
                                                 where p.products_id = :products_id
                                                 and pqt.language_id = :language_id
                                                 and pqt.products_quantity_unit_id = p.products_quantity_unit_id
                                                ');
      $QproductQtyUnit->bindInt(':products_id', $_GET['pID']);
      $QproductQtyUnit->bindInt(':language_id', $CLICSHOPPING_Language->getId());
      $QproductQtyUnit->execute();

      return $QproductQtyUnit->value('products_quantity_unit_title');
    }
  }