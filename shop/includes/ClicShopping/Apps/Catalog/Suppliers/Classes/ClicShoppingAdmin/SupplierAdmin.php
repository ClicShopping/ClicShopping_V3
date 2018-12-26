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

  namespace ClicShopping\Apps\Catalog\Suppliers\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;

  class SupplierAdmin {

    protected $supplier_id;
    protected $language_id;
    protected $db;

    public function __construct() {
      $this->db = Registry::get('Db');
    }


/**
 * the supplier_url
 *
 * @param string  $supplier_id, $language_id
 * @return string $supplier['supplier_description'],  description of the supplier
 * @access public
 */
    public function getSupplierUrl($supplier_id, $language_id) {

      $Qsuppliers = $this->db->prepare('select suppliers_url
                                         from :table_suppliers_info
                                         where suppliers_id = :suppliers_id
                                         and languages_id = :language_id
                                       ');
      $Qsuppliers->bindInt(':suppliers_id', (int)$supplier_id);
      $Qsuppliers->bindInt(':language_id', (int)$language_id);

      $Qsuppliers->execute();

      return $Qsuppliers->value('suppliers_url');
    }
  }