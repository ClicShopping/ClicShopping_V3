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

  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class Authentification
  {
    public $username;
    public $key;
    public $ip;

    public function __construct(string $username, string $key, ?string $ip)
    {
     $this->username = $username;
     $this->key = $key;
     $this->ip = $ip;
    }

    /**
     * @return array|bool
     */
    public function checkAccess(): array | bool
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
      $Qapi->bindValue(':username', $this->username);
      $Qapi->bindValue(':api_key', $this->key);

      $Qapi->execute();

      $result = $Qapi->fetch();

      if (is_array($result)) {
        return $result;
      } else {
        return false;
      }
    }

    /**
     * @param int $api_id
     * @return int|string
     * @throws \Exception
     */
    public static function addSession(int $api_id): int|string
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->get('api_session', 'session_id', ['api_id' => $api_id]);

      if (empty($Qcheck->value('session_id'))) {
        $session_id = bin2hex(random_bytes(16));
        $ip = HTTP::getIpAddress();

        $sql_data_array = [
          'api_id' => $api_id,
          'session_id' => $session_id,
          'ip' => $ip,
          'date_added' => 'now()',
          'date_modified' => 'now()'
        ];

        $CLICSHOPPING_Db->save(':table_api_session', $sql_data_array);

        return $CLICSHOPPING_Db->lastInsertId();
      } else {
        $Qcheck = $CLICSHOPPING_Db->get('api_session', 'session_id', ['api_id' => $api_id]);
        $token = $Qcheck->value('session_id');

        return $token;
      }
    }

    /**
     * @param int $api_id
     * @return array
     */
    public static function getIps(int $api_id): bool
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->get('api_ip', 'ip', ['api_id' => $api_id]);

      if ($Qcheck->value('ip') == '127.0.0.1') {
        return true;
      } elseif($Qcheck->value('ip') == HTTP::getIpAddress()) {
        return true;
      } else {
        return false;
      }

      return true;
    }

    /**
     * @param string $string
     * @return void
     */
    public function checkUrl(string $string)
    {
      $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
      $uri = explode( '/', $uri );

      $path = CLICSHOPPING::getConfig('http_path', 'Shop');
      $path = str_replace('/', '', $path);

// all of our endpoints start with /data
// everything else results in a 404 Not Found
      if ($uri[1] !== $path) {
        header("HTTP/1.1 404 Not Found");
        exit();
      }

// check request
      if(!isset($_REQUEST['api'])) {
        header("HTTP/1.1 404 Not Found");
        exit();
      }

      if(isset($_REQUEST[$string])) {
        header("HTTP/1.1 404 Not Found");
        exit();
      }

      return true;
    }
  }