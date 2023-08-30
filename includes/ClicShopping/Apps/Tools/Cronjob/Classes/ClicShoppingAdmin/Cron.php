<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\Cronjob\Classes\ClicShoppingAdmin;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class Cron
{

  /**
   * @var mixed|null
   */
  private $Cronjob;

  public function __construct()
  {
    // $this->Cronjob = Registry::get('Cronjob');
  }

  /**
   * @param array|null $data
   * @param int|null $id
   * @return array
   */
  public static function getCrons(?array $data = [], ?int $id): array
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $limit = '';
    $where = '';

    if (!\is_null($id)) {
      $where = 'where cron_id = ' . HTML::sanitize($_GET['cronId']);
    }

    if (isset($data['start']) || isset($data['limit'])) {
      if ($data['start'] < 0) {
        $data['start'] = 0;
      }

      if ($data['limit'] < 1) {
        $data['limit'] = 20;
      }

      $limit = 'limit ' . (int)$data['start'] . "," . (int)$data['limit'];
    }

    $Qcron = $CLICSHOPPING_Db->prepare('select SQL_CALC_FOUND_ROWS cron_id,
                                                                      code,
                                                                      cycle,
                                                                      action,
                                                                      status,
                                                                      date_added,
                                                                      date_modified
                                             from :table_cron
                                             ' . $where . '    
                                             ' . $limit . '
                                          ');

    $Qcron->execute();

    $cron_array = $Qcron->fetchAll();

    return $cron_array;
  }

  /**
   * @param string $code
   * @return int
   */
  public static function getCronCode(string $code): int
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $QcronCode = $CLICSHOPPING_Db->prepare('select cron_id
                                              from :table_cron
                                              where code = :code
                                             ');

    $QcronCode->bindValue(':code', $code);
    $QcronCode->execute();

    $result = $QcronCode->valueInt('cron_id');

    return $result;
  }

  /**
   * @param int $cron_id
   */
  public static function updateCron(int $cron_id): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $sql_data_array = ['date_modified' => 'now()'];

    $update_array = ['cron_id' => $cron_id];

    $CLICSHOPPING_Db->save('cron', $sql_data_array, $update_array);
  }

  /**
   * @return int
   */
  public static function getTotalCrons(): int
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qquery = $CLICSHOPPING_Db->prepare('select count(*) as total 
                                           from :table_cron
                                          ');
    $Qquery->execute();

    return $Qquery->valueInt('total');
  }

  /**
   * @param int $cron_id
   * @param int $status
   * @return string status on or off
   */
  public static function getCronjobStatus(int $cron_id, int $status)
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    if ($status == '0') {
      return $CLICSHOPPING_Db->save('cron',
        ['status' => 1,
          'date_modified' => 'now()'
        ],
        ['cron_id' => (int)$cron_id]
      );
    } elseif ($status == '1') {
      return $CLICSHOPPING_Db->save('cron',
        ['status' => 0,
          'date_modified' => 'now()'
        ],
        ['cron_id' => (int)$cron_id]
      );
    } else {
      return -1;
    }
  }
}
