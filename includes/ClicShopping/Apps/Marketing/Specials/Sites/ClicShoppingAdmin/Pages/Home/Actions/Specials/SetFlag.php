<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */


  namespace ClicShopping\Apps\Marketing\Specials\Sites\ClicShoppingAdmin\Pages\Home\Actions\Specials;

  use ClicShopping\OM\Registry;

  class SetFlag extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Specials = Registry::get('Specials');

      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

      if (isset($_GET['flag'], $_GET['id'])) {
        static::getSpecialsStatus($_GET['id'], $_GET['flag']);
      }

      $CLICSHOPPING_Specials->redirect('Specials&page=' . $page . 'sID=' . (int)$_GET['id']);
    }

    /**
     * @param int $specials_id
     * @param int $status
     * @return int
     */
    Public static function getSpecialsStatus(int $specials_id, int $status)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if ($status == 1) {
        return $CLICSHOPPING_Db->save('specials', [
          'status' => 1,
          'specials_date_added' => 'null',
          'specials_last_modified' => 'null',
          'scheduled_date' => 'null',
          'expires_date' => 'null'
        ],
          ['specials_id' => (int)$specials_id]
        );

      } elseif ($status == 0) {
        return $CLICSHOPPING_Db->save('specials', [
          'status' => 0,
          'specials_last_modified' => 'now()'
        ],
          ['specials_id' => (int)$specials_id]
        );
      } else {
        return -1;
      }
    }
  }