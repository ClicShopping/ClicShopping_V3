<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Api\Classes\Shop;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTTP;
use ClicShopping\OM\Registry;

class Authentification
{
  public $username;
  public $key;
  public $ip;

  /**
   * Constructor for initializing the class with user credentials and optional IP address.
   *
   * @param string $username The username for authentication.
   * @param string $key The API key or password associated with the username.
   * @param string|null $ip Optional IP address for further security or identification purposes.
   *
   * @return void
   */
  public function __construct(string $username, string $key, ?string $ip)
  {
    $this->username = $username;
    $this->key = $key;
    $this->ip = $ip;
  }

  /**
   * Checks the access credentials against the database to validate the user's API access.
   *
   * @return array|bool Returns an associative array containing API details if the credentials are valid; otherwise, returns false.
   */
  public function checkAccess(): array|bool
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
   * Adds a new API session for the provided API ID or returns an existing session ID.
   *
   * @param int $api_id The unique identifier of the API for which the session is being created or retrieved.
   * @return int|string Returns the unique session ID of the newly created session or an existing session ID.
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
   * Retrieves and checks the IP address associated with the given API ID.
   *
   * @param int $api_id The API ID used to retrieve the associated IP address.
   * @return bool Returns true if the IP address matches '127.0.0.1' or the client's IP address. Returns false otherwise.
   */
  public static function getIps(int $api_id): bool
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qcheck = $CLICSHOPPING_Db->get('api_ip', 'ip', ['api_id' => $api_id]);

    if ($Qcheck->value('ip') == '127.0.0.1') {
      return true;
    } elseif ($Qcheck->value('ip') == HTTP::getIpAddress()) {
      return true;
    } else {
      return false;
    }

    return true;
  }

  /**
   * Validates the given URL path and request parameters to ensure they match the application's
   * expected structure and configuration. Sends a 404 Not Found response if the validation fails.
   *
   * @param string $string The key to check within the request parameters for validation.
   * @return bool Returns true if the validation passes; terminates execution otherwise with a 404 response.
   */
  public function checkUrl(string $string)
  {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = explode('/', $uri);

    $path = CLICSHOPPING::getConfig('http_path', 'Shop');
    $path = str_replace('/', '', $path);

// all of our endpoints start with /data
// everything else results in a 404 Not Found
    if ($uri[1] !== $path) {
      header("HTTP/1.1 404 Not Found");
      exit();
    }

// check request
    if (!isset($_REQUEST['api'])) {
      header("HTTP/1.1 404 Not Found");
      exit();
    }

    if (isset($_REQUEST[$string])) {
      header("HTTP/1.1 404 Not Found");
      exit();
    }

    return true;
  }
}