<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Api\Sites\Shop\Pages\Manufacturers;

use ClicShopping\Apps\Configuration\Api\Classes\Shop\ApiShop;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class Manufacturers extends \ClicShopping\OM\PagesAbstract
{
  protected ?string $file = null;
  protected bool $use_site_template = false;
  private mixed $lang;
  private mixed $db;

  /**
   * Initializes and handles API requests based on the HTTP request method.
   * This includes interacting with the database to validate tokens and
   * performing operations such as GET, DELETE, POST, or PUT for manufacturers.
   *
   * @return bool|void Returns false if the API application status is not enabled,
   *                   otherwise it processes the request and exits.
   */
  protected function init()
  {
    $this->lang = Registry::get('Language');
    $this->Db = Registry::get('Db');

    if (!\defined('CLICSHOPPING_APP_API_AI_STATUS') && CLICSHOPPING_APP_API_AI_STATUS == 'False') {
      return false;
    }

    $requestMethod = ApiShop::requestMethod();

// Handle the event
    switch ($requestMethod) {
      case 'GET':
        $token = HTML::sanitize($_GET['token']);
        $result = ApiShop::checkToken($token);
        $check = $this->statusCheck('get_manufacturer_status', $token);

        if (empty($result) || $check == 0) {
          $response = ApiShop::notFoundResponse();
          Registry::get('Session')->kill();
        } else {
          $response = static::getManufacturer();
        }
        break;
      case 'DELETE':
        $token = HTML::sanitize($_GET['token']);
        $result = ApiShop::checkToken($token);

        $check = $this->statusCheck('delete_manufacturer_status', $token);

        if (empty($result) || $check == 0) {
          $response = ApiShop::notFoundResponse();
          Registry::get('Session')->kill();
        } else {
          $response = static::deleteManufacturer();
        }
        break;
      case 'POST':
        $token = HTML::sanitize($_GET['token']);
        $result = ApiShop::checkToken($token);

        if (isset($_GET['update'])) {
          $check = $this->statusCheck('update_manufacturer_status', $token);

          if (empty($result) || $check == 0) {
            $response = ApiShop::notFoundResponse();
            Registry::get('Session')->kill();
          } else {
            $response = static::saveManufacturer();  
          }
        } elseif (isset($_GET['update'])) {
          $check = $this->statusCheck('insert_manufacturer_status', $token);

          if (empty($result) || $check == 0) {
            $response = ApiShop::notFoundResponse();
            Registry::get('Session')->kill();
          } else {
            $response = static::saveManufacturer();  
          }
        }
        break;
      case 'PUT':
        break;
      default:
        $response = ApiShop::notFoundResponse();
        Registry::get('Session')->kill();
        break;
    }

    if ($response['body']) {
      echo $response['body'];
    }

    exit;
  }

  /**
   * Retrieves the manufacturer information by calling the appropriate API hook and handles the response.
   *
   * @return array Returns an array containing the API response, which could either be the data for the manufacturer
   *               or a response indicating that the requested data was not found.
   */
  private static function getManufacturer(): array
  {
    $CLICSHOPPING_Hooks = Registry::get('Hooks');

    $result = $CLICSHOPPING_Hooks->call('Api', 'ApiGetManufacturer');

    if (empty($result)) {
      $response = ApiShop::notFoundResponse();
    } else {
      $response = ApiShop::HttpResponseOk($result);
    }

    ApiShop::clearCache();

    return $response;
  }

  /**
   * Executes the deletion of a manufacturer via the API and returns the HTTP response.
   *
   * The method interacts with the Hooks system to perform the manufacturer deletion
   * and generates an appropriate HTTP response based on the result. It also clears
   * the cache after the operation.
   *
   * @return array The HTTP response indicating the result of the operation.
   */
  private static function deleteManufacturer(): array
  {
    $CLICSHOPPING_Hooks = Registry::get('Hooks');

    $result = $CLICSHOPPING_Hooks->call('Api', 'ApiDeleteManufacturer');

    if (empty($result)) {
      $response = ApiShop::notFoundResponse();
    } else {
      $response = ApiShop::HttpResponseOk($result);
    }

    ApiShop::clearCache();

    return $response;
  }

  /**
   * Saves the manufacturer data by calling the associated hook and returns the appropriate response.
   *
   * @return array Returns an HTTP response array indicating the status of the operation or the result data.
   */
  private static function saveManufacturer(): array
  {
    $CLICSHOPPING_Hooks = Registry::get('Hooks');

    $result = $CLICSHOPPING_Hooks->call('Api', 'ApiSaveManufacturer');

    if (empty($result)) {
      $response = ApiShop::notFoundResponse();
    } else {
      $response = ApiShop::HttpResponseOk($result);
    }

    ApiShop::clearCache();

    return $response;
  }

  /**
   * Executes a database query to check the status of a specific column tied to a session.
   *
   * @param string $string The name of the column to be selected from the database.
   * @param string $token The session identifier used to retrieve the associated data.
   * @return int Returns the integer value of the specified column resulting from the query.
   */
  private function statusCheck(string $string, string $token): int
  {
    $QstatusCheck = $this->Db->prepare('select a.' . $string . '
                                          from :table_api a,
                                               :table_api_session ase
                                          where a.api_id = ase.api_id
                                          and ase.session_id = :session_id  
                                        ');
    $QstatusCheck->bindValue('session_id', $token);

    $QstatusCheck->execute();

    $result = $QstatusCheck->valueInt($string);

    return $result;
  }
}
