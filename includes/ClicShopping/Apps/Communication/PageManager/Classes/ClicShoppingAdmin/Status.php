<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Communication\PageManager\Classes\ClicShoppingAdmin;

use ClicShopping\OM\Registry;
/**
 * Status modification of page manager - Sets the status of a page
 *
 * This method updates the status of a specified page in the pages_manager table.
 * It can either enable (status = 1) or disable (status = 0) the page. The method
 * also updates relevant fields such as `date_status_change`, and optionally,
 * `page_date_closed` depending on the status passed.
 *
 * @param int $pages_id The ID of the page to update
 * @param int $status The desired status for the page (1 for active, 0 for inactive)
 * @return string|int Updates the database and returns an indication of success or failure (-1 for invalid status)
 */
class Status
{
  protected int $pages_id;

  /**
   * Updates the status of a page in the pages_manager table based on the provided status.
   *
   * @param int $pages_id The ID of the page to update.
   * @param int $status The desired status to set for the page.
   *                    Use 1 to activate the page and 0 to deactivate it.
   *                    Any other value will result in no changes and will return -1.
   *
   * @return mixed Returns the result of the save operation when the status is set to 1 or 0.
   *               Returns -1 for invalid status values.
   */
  public static function getPageManagerStatus(int $pages_id, int $status)
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