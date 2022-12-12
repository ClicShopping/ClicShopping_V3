<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\Api\Classes\Shop;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\DateTime;

  use ClicShopping\OM\Cache;

  class ApiShop
  {
    /**
     * @return mixed GET/POST ...
     */
    public static function requestMethod(): mixed
    {
      $requestMethod = $_SERVER["REQUEST_METHOD"];

      return $requestMethod;
    }

    /**
     * @param string $username
     * @param string $key
     * @return array
     */
    public static function getAccess(string $username, string $key): bool
    {
      $CLICSHOPPING_Db = Registry::get('Db');
     
      $Qapi = $CLICSHOPPING_Db->prepare('select api_id,
                                              username,
                                              api_key,
                                              status,
                                              date_added,
                                              date_modified
                                       from :table_api
                                       where status = 1
                                       and username = :username
                                       and api_key = :api_key
                                      ');
      $Qapi->bindValue(':username', $username);
      $Qapi->bindValue(':api_key', $key);

      $Qapi->execute();
      $result = $Qapi->fetch();

      return $result;
    }

    /**
     * @param int|null $api_id
     * @return int
     * @throws \Exception
     */
    public static function createSession(?int $api_id): int
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Ip = HTTP::getIpAddress();

      $session_id = bin2hex(random_bytes(16));

      $sql_data_array = [
        'api_id' => HTML::sanitize($api_id),
        'session_id' => $session_id,
        'ip' => $Ip,
        'date_added' => 'now()',
        'date_modified' => 'now()'
      ];

      $CLICSHOPPING_Db->save('api_session', $sql_data_array);

      return $CLICSHOPPING_Db->lastInsertId();
    }

    /**
     * @return int|false|null
     */
    public function getUrlId(): int|false|null
    {
      $request = HTML::sanitize($_REQUEST['id']);
      if(isset($request) && is_numeric($request)) {
        $result = (int)$request;
      } else {
        if (!empty($request)) {
          return false;
        }

        $result = null;
      }

      return $result;
    }

    /**
     * @param string $token
     * @return string
     * @throws \Exception
     */
    public static function checkToken(string $token): string
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $sql_data_array = [
        'api_id',
        'date_modified',
        'date_added'
      ];

      $Qcheck = $CLICSHOPPING_Db->get('api_session', $sql_data_array, ['session_id' => $token], 1);

      if (!empty($Qcheck->value('api_id'))) {
        $now = date('Y-m-d H:i:s');
        $date_diff = DateTime::getIntervalDate($Qcheck->value('date_modified'), $now);

        if ($date_diff > (3600/60)) {
          $CLICSHOPPING_Db->delete('api_session', ['api_id' => (int)$Qcheck->valueInt('api_id')]);

          $session_id = bin2hex(random_bytes(16));
          $Ip = HTTP::getIpAddress();

          $sql_data_array = [
            'api_id' => $Qcheck->valueInt('api_id'),
            'session_id' => $session_id,
            'date_modified' => 'now()',
            'date_added' => $Qcheck->value('date_added'),
            'ip' => $Ip
          ];

          $CLICSHOPPING_Db->save('api_session', $sql_data_array);

          $token = $session_id;
        }
      } else {
        $session_id = bin2hex(random_bytes(16));
        $Ip = HTTP::getIpAddress();

        $sql_data_array = [
          'api_id' => $Qcheck->valueInt('api_id'),
          'session_id' => $session_id,
          'date_modified' => 'now()',
          'date_added' => $Qcheck->value('date_added'),
          'ip' => $Ip
        ];

        $CLICSHOPPING_Db->save('api_session', $sql_data_array);

        $token = $session_id;
      }

      return $token;
    }

    /**
     *
     */
    public static function clearCache(): void
    {
      Cache::clear('categories');
      Cache::clear('products-also_purchased');
      Cache::clear('upcoming');
    }

    /**
     * @return array
     */
    public static function notFoundResponse(): array
    {
      $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
      $response['body'] = json_encode(['error' => 'HTTP/1.1 404 Not Found']);

      return $response;
    }

    /**
     * @param array $result
     * @return array
     */
    public static function HttpResponseOk(array $result): array
    {
      $response['status_code_header'] = 'HTTP/1.1 200 OK';
      $response['body'] = json_encode($result);

      return $response;
    }
  }