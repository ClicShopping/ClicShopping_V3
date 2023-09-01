<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Suppliers\Classes\ClicShoppingAdmin;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;
use function is_null;

class SupplierAdmin
{
  private mixed $db;

  public function __construct()
  {
    $this->db = Registry::get('Db');
  }

  /**
   * @return array|bool
   */
  public function getSupplier(): array|bool
  {
    if (isset($_GET['pID'])) {
      $pID = HTML::sanitize($_GET['pID']);

      $Qproducts = $this->db->prepare('select suppliers_id
                                              from :table_products
                                              where products_id = :products_id
                                            ');
      $Qproducts->bindInt(':products_id', HTML::sanitize($pID));

      $Qproducts->execute();

      $Qsuppliers = $this->db->prepare('select suppliers_id,
                                                       suppliers_name
                                                from :table_suppliers
                                                where suppliers_id = :suppliers_id
                                              ');
      $Qsuppliers->bindInt(':suppliers_id', $Qproducts->valueInt('suppliers_id'));
      $Qsuppliers->execute();

      $result = $Qsuppliers->fetchAll();

      return $result;
    } else {
      return false;
    }
  }

  /**
   * the supplier_url
   *
   * @param int|null $supplier_id , $language_id
   * @param int $language_id
   * @return string $supplier['supplier_description'],  description of the supplier
   */
  public function getSupplierUrl(?int $supplier_id, int $language_id): string
  {
    if (!is_null($supplier_id)) {
      $Qsuppliers = $this->db->prepare('select suppliers_url
                                           from :table_suppliers_info
                                           where suppliers_id = :suppliers_id
                                           and languages_id = :language_id
                                         ');
      $Qsuppliers->bindInt(':suppliers_id', $supplier_id);
      $Qsuppliers->bindInt(':language_id', $language_id);

      $Qsuppliers->execute();

      return $Qsuppliers->value('suppliers_url');
    } else {
      return '';
    }
  }

  /**
   * the supplier name
   *
   * @param string|null $supplier_name
   * @return int|string supplier_id
   */
  public function getSupplierId(?string $supplier_name = null): int|string
  {
    if (!is_null($supplier_name)) {
      $Qsuppliers = $this->db->prepare('select suppliers_id
                                          from :table_suppliers
                                          where suppliers_name = :suppliers_name
                                          limit 1
                                       ');
      $Qsuppliers->bindValue(':suppliers_name', $supplier_name);

      $Qsuppliers->execute();

      return $Qsuppliers->value('suppliers_id');
    } else {
      return $suppliers_id = 0;
    }
  }
}