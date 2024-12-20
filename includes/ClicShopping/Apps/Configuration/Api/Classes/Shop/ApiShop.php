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

use ClicShopping\OM\Cache;
use ClicShopping\OM\DateTime;
use ClicShopping\OM\HTML;
use ClicShopping\OM\HTTP;
use ClicShopping\OM\Registry;

class ApiShop
{
  /**
   *
   * @return mixed Returns the request method used in the HTTP request, typically a string such as 'GET', 'POST', etc.
   */
  public static function requestMethod(): mixed
  {
    $requestMethod = $_SERVER["REQUEST_METHOD"];

    return $requestMethod;
  }

  /**
   * Checks if the given username and API key grant access.
   *
   * @param string $username The username to authenticate.
   * @param string $key The API key associated with the username.
   * @return bool Returns true if access is granted, otherwise false.
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
   * Creates a new API session and stores it in the database.
   *
   * @param int|null $api_id The API ID associated with the session. Nullable.
   * @return int The ID of the newly created session entry in the database.
   */
  public static function createSession( int|null $api_id): int
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
   * Retrieves the URL parameter 'id' from the request, sanitizes it, and returns it as an integer.
   * If the parameter is non-numeric but not empty, returns false.
   * If the parameter is empty or not set, returns null.
   *
   * @return int|false|null Returns the sanitized integer value of 'id', false if non-numeric but not empty, or null if empty or not set.
   */
  public function getUrlId(): int|false|null
  {
    $request = HTML::sanitize($_REQUEST['id']);
    if (isset($request) && is_numeric($request)) {
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
   * Validates and regenerates the API session token if necessary or creates
   * a new session if the token is invalid.
   *
   * @param string $token The current session token to check or renew.
   * @return string The valid session token, either existing or newly generated.
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

      if ($date_diff > (3600 / 60)) {
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
        'date_added' => 'now()',
        'ip' => $Ip
      ];

      $CLICSHOPPING_Db->save('api_session', $sql_data_array);

      $token = $session_id;
    }

    return $token;
  }

  /**
   * Clears specific cached data for categories, products also purchased, and upcoming items.
   *
   * @return void
   */
  public static function clearCache(): void
  {
    Cache::clear('categories');
    Cache::clear('products-also_purchased');
    Cache::clear('upcoming');
  }

  /**
   * Generates a 404 Not Found HTTP response.
   *
   * @return array Returns an array containing the status code header and a JSON-encoded body with an error message.
   */
  public static function notFoundResponse(): array
  {
    $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
    $response['body'] = json_encode(['error' => 'HTTP/1.1 404 Not Found']);

    return $response;
  }

  /**
   * Generates an HTTP response with a status code header of '200 OK' and a JSON-encoded body containing the provided result.
   *
   * @param array $result The array of data to be included in the response body.
   * @return array An associative array containing the response header and body.
   */
  public static function HttpResponseOk(array $result): array
  {
    $response['status_code_header'] = 'HTTP/1.1 200 OK';
    $response['body'] = json_encode($result);

    return $response;
  }
}