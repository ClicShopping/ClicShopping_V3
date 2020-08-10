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

  namespace ClicShopping\Apps\Catalog\Manufacturers\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;

  class Status
  {

    protected $status;
    protected $manufacturers_id;

    /**
     * Status products manufacturers  - Sets the status of a product on manufacturers
     *
     * @param string manufacturers_id, status
     * @return string status on or off
     * @access public
     */

    Public static function getManufacturersStatus(int $manufacturers_id, int $status)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if ($status == '1') {

        return $CLICSHOPPING_Db->save('manufacturers', ['manufacturers_status' => 1,
          'date_added' => 'null',
          'last_modified' => 'null'],
          ['manufacturers_id' => (int)$manufacturers_id]
        );

      } elseif ($status == '0') {

        return $CLICSHOPPING_Db->save('manufacturers', ['manufacturers_status' => 0,
          'last_modified' => 'now()'],
          ['manufacturers_id' => (int)$manufacturers_id]
        );

      } else {
        return -1;
      }
    }
  }