<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\WhosOnline\Classes\ClicShoppingAdmin;

use ClicShopping\OM\Registry;

class WhosOnlineAdmin
{
  /**
   * @return int
   */
  public static function getCountWhosOnline(): int
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    if (isset($_SESSION['admin'])) {
      static::delete();

      $QwhosOnline = $CLICSHOPPING_Db->prepare('select distinct session_id,
                                                                  customer_id,
                                                                  full_name,
                                                                  ip_address,
                                                                  time_entry,
                                                                  time_last_click,
                                                                  last_page_url
                                                   from :table_whos_online
                                                ');
      $QwhosOnline->execute();

      $whosOnlineNumber = $QwhosOnline->rowCount();

      return $whosOnlineNumber;
    }
  }

  /**
   * @return void
   */
  private static function delete(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');

// Session time before expiration
    $xx_mins_ago = (time() - 900);

// Delete data after expiration time
    $Qdelete = $CLICSHOPPING_Db->prepare('delete
                                            from :table_whos_online
                                            where  time_last_click <= :time_last_click
                                          ');
    $Qdelete->bindValue(':time_last_click', $xx_mins_ago);
    $Qdelete->execute();
  }

  /**
   * @return void
   */
  public static function removeWhoOnline(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $xx_mins_ago = (time() - 900);

    $Qclean = $CLICSHOPPING_Db->prepare('delete
                                           from :table_whos_online
                                           where time_last_click = :time_last_click
                                          ');
    $Qclean->bindValue(':time_last_click', $xx_mins_ago);
    $Qclean->execute();
  }
}