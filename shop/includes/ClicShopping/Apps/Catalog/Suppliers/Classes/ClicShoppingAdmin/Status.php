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

  namespace ClicShopping\Apps\Catalog\Suppliers\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;

  class Status
  {

    protected $languages_id;
    protected $suppliers_id;

    /**
     * Status products suppliers  - Sets the status of a product on suppliers
     *
     * @param string suppliers_id, status
     * @return string status on or off
     * @access public
     */

    public static function getSuppliersStatus(int $suppliers_id,int $status)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if ($status == 1) {

        return $CLICSHOPPING_Db->save('suppliers', ['suppliers_status' => 1,
          'date_added' => 'null',
          'last_modified' => 'null'
        ],
          ['suppliers_id' => (int)$suppliers_id]
        );

      } elseif ($status == 0) {

        return $CLICSHOPPING_Db->save('suppliers', ['suppliers_status' => 0,
          'last_modified' => 'now()'
        ],
          ['suppliers_id' => (int)$suppliers_id]
        );
      } else {
        return -1;
      }
    }
  }