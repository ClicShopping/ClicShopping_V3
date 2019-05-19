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

  namespace ClicShopping\Apps\Communication\PageManager\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;

  class Status
  {

    protected $pages_id;


    /**
     * Status modification of page manager - Sets the status of a page
     *
     * @param string pages_id, status
     * @return string status on or off
     * @access public
     */
    Public static function getPageManagerStatus($pages_id, $status)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if ($status == '1') {

        return $CLICSHOPPING_Db->save('pages_manager', ['status' => 1,
          'page_date_closed' => 'null',
          'date_status_change' => 'now()'
        ],
          ['pages_id' => (int)$pages_id]
        );

      } elseif ($status == '0') {

        return $CLICSHOPPING_Db->save('pages_manager', ['status' => 0,
          'date_status_change' => 'now()'
        ],
          ['pages_id' => (int)$pages_id]
        );

      } else {
        return -1;
      }
    }
  }