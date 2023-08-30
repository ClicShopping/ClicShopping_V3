<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Api\Classes\ClicShoppingAdmin;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class ApiAdmin
{
  private mixed $db;

  public function __construct()
  {
    $this->db = Registry::get('Db');
  }

  /**
   * @param array $data
   * @return int
   */
  public function addApi(array $data): int
  {
    $sql_data_array = [
      'username' => HTML::sanitize($data['username']),
      'api_key' => HTML::sanitize($data['api_key']),
      'status' => (int)$data['status'],
      'date_added' => 'now()',
      'date_modified' => 'now()'
    ];

    $this->db->save('api', $sql_data_array);

    $api_id = $this->db->lastInsertId();

    if (isset($data['api_ip'])) {
      foreach ($data['api_ip'] as $ip) {
        if ($ip) {
          $insert_data_array = [
            'api_id' => (int)$api_id,
            'ip' => HTML::sanitize($ip)
          ];

          $this->db->save('api_ip', $insert_data_array);
        }
      }
    }

    return $api_id;
  }

  /**
   * @param int $api_id
   * @param array $data
   * @return void
   */
  public function editApi(int $api_id, array $data): void
  {
    $sql_data_array = [
      'username' => HTML::sanitize($data['username']),
      'api_key' => HTML::sanitize($data['api_key']),
      'status' => (int)$data['status'],
      'date_modified' => 'now()'
    ];

    $update_array_sql = [
      'api_id' => (int)$api_id
    ];

    $this->db->save('api', $sql_data_array, $update_array_sql);

    $delete_sql_array = [
      'api_id' => (int)$api_id
    ];

    $this->db->delete('api_ip', $delete_sql_array);


    if (isset($data['api_ip'])) {
      foreach ($data['api_ip'] as $ip) {
        if ($ip) {
          $insert_data_array = [
            'api_id' => (int)$api_id,
            'ip' => HTML::sanitize($ip)
          ];

          $this->db->save('api_id', $insert_data_array);
        }
      }
    }
  }

  /**
   * @param int $api_id
   * @return void
   */
  public function deleteApi(int $api_id): void
  {
    $delete_sql_array = [
      'api_id' => (int)$api_id
    ];

    $this->db->delete('api', $delete_sql_array);
  }

  /**
   * @param int $api_id
   * @return array
   */
  public function getApi(int $api_id): array
  {
    $Qapi = $this->app->db->prepare('select api_id,
                                              username,
                                              api_key,
                                              status,
                                              date_added,
                                              date_modified
                                       from :table_api
                                       where api_id = :api_id
                                      ');
    $Qapi->bindint(':api_id', $api_id);
    $Qapi->execute();

    return $Qapi->fetchAll();
  }

  /**
   * @param array $data
   * @return array
   */
  public function getAllApi(): array
  {
    $Qapi = $this->app->db->prepare('select api_id,
                                              username,
                                              api_key,
                                              status,
                                              date_added,
                                              date_modified
                                       from :table_api
                                       where api_id = :api_id
                                      ');

    $Qapi->execute();

    return $Qapi->fetchAll();
  }

  /**
   * @return int
   */
  public function getTotalApis(): int
  {
    $Qapi = $this->app->db->prepare('select COUNT(*) as total
                                       from :table_api
                                      ');

    $Qapi->execute();

    return $Qapi->valueInt('total');
  }

  /**
   * @param int $api_id
   * @param string $ip
   * @return void
   */
  public function addIp(int $api_id, string $ip): void
  {
    $insert_sql_array = [
      'api_id' => (int)$api_id,
      'ip' => HTML::sanitize($ip)
    ];

    $this->db->save('api_ip', $insert_sql_array);
  }

  /**
   * @param int $api_id
   * @return array
   */
  public function getIps(int $api_id): array
  {
    $ip_data = [];

    $Qapi = $this->app->db->prepare('select *
                                       from :table_api_ip
                                       where :api_id = :api_id
                                      ');
    $Qapi->bindint(':api_id', $api_id);

    $Qapi->execute();

    foreach ($Qapi->fetch() as $result) {
      $ip_data[] = $result['ip'];
    }

    return $ip_data;
  }

  /**
   * @param int $api_id
   * @param string $session_id
   * @param string $ip
   * @return int
   */
  public function addSession(int $api_id, string $session_id, string $ip): int
  {
    $Qapi = $this->app->db->prepare('select api_ip_id,
                                              api_id,
                                              ip
                                       from :table_api_ip
                                       where ip = :ip
                                      ');
    $Qapi->bindint(':ip', $ip);
    $Qapi->execute();

    if (!$Qapi->fetch()) {
      $insert_sql_array = [
        'api_id' => (int)$api_id,
        'ip' => HTML::sanitize($ip)
      ];

      $this->db->save('api_ip', $insert_sql_array);
    }

    $insert_sql_array = [
      'api_id' => (int)$api_id,
      'session_id' => HTML::sanitize($session_id),
      'ip' => HTML::sanitize($ip),
      'date_added' => 'now()',
      'date_modified' => 'now()'
    ];

    $this->db->save('api_session', $insert_sql_array);

    return $this->db->lastInsertId();
  }

  /**
   * @param int $api_id
   * @return array
   */
  public function getSessions(int $api_id): array
  {
    $Qapi = $this->app->db->prepare('select api_session_id,
                                              api_id,
                                              session_id,
                                              ip,
                                              date_added,
                                              date_modified
                                       from :table_api_session
                                       where api_id = :api_id
                                      ');
    $Qapi->bindint(':api_id', $api_id);
    $Qapi->execute();

    $result = $Qapi->fetchAll();

    return $result;
  }

  /**
   * @param int $api_session_id
   * @return void
   */
  public function deleteSession(int $api_session_id): void
  {
    $delete_sql_array = [
      'api_session_id' => $api_session_id
    ];

    $this->db->delete('api_session', $delete_sql_array);
  }

  /**
   * @param string $session_id
   * @return void
   */
  public function deleteSessionBySessionId(string $session_id): void
  {
    $delete_sql_array = [
      'session_id' => HTML::sanitize($session_id)
    ];

    $this->db->delete('api_session', $delete_sql_array);
  }
}