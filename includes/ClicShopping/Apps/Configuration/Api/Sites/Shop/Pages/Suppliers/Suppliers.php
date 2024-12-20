<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Api\Sites\Shop\Pages\Suppliers;

use ClicShopping\Apps\Configuration\Api\Classes\Shop\ApiShop;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class Suppliers extends \ClicShopping\OM\PagesAbstract
{
  protected ?string $file = null;
  protected bool $use_site_template = false;
  private mixed $lang;
  private mixed $db;

  /**
   * Initializes the API handler by setting up necessary dependencies and processing
   * the incoming HTTP request based on the request method. Executes appropriate actions
   * (GET, POST, DELETE, etc.) related to supplier management while ensuring token validation.
   *
   * @return bool|void Returns false if the application status is not enabled,
   *                   or outputs the appropriate response and terminates further execution.
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
        $check = $this->statusCheck('get_supplier_status', $token);

        if (empty($result) || $check == 0) {
          $response = ApiShop::notFoundResponse();
          Registry::get('Session')->kill();
        } else {
          $response = static::getSupplier();
        }
        break;
      case 'DELETE':
        $token = HTML::sanitize($_GET['token']);
        $result = ApiShop::checkToken($token);

        $check = $this->statusCheck('delete_supplier_status', $token);

        if (empty($result) || $check == 0) {
          $response = ApiShop::notFoundResponse();
          Registry::get('Session')->kill();
        } else {
          $response = static::deleteSupplier();
        }
        break;
      case 'POST':
        $token = HTML::sanitize($_GET['token']);
        $result = ApiShop::checkToken($token);

        if (isset($_GET['update'])) {
          $check = $this->statusCheck('update_supplier_status', $token);

          if (empty($result) || $check == 0) {
            $response = ApiShop::notFoundResponse();
            Registry::get('Session')->kill();
          } else {
            $response = static::saveSupplier();  
          }
        } elseif (isset($_GET['update'])) {
          $check = $this->statusCheck('insert_supplier_status', $token);

          if (empty($result) || $check == 0) {
            $response = ApiShop::notFoundResponse();
            Registry::get('Session')->kill();
          } else {
            $response = static::saveSupplier();  
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
   * Retrieves supplier information by invoking the 'ApiGetSupplier' hook. If no data is found,
   * it prepares a "not found" response; otherwise, it returns an HTTP OK response with the retrieved data.
   * Clears the cache after processing.
   *
   * @return array The HTTP response containing either the supplier data or a "not found" message.
   */
  private static function getSupplier(): array
  {
    $CLICSHOPPING_Hooks = Registry::get('Hooks');

    $result = $CLICSHOPPING_Hooks->call('Api', 'ApiGetSupplier');

    if (empty($result)) {
      $response = ApiShop::notFoundResponse();
    } else {
      $response = ApiShop::HttpResponseOk($result);
    }

    ApiShop::clearCache();

    return $response;
  }

  /**
   * Deletes a supplier by invoking the appropriate API hook and handles the HTTP response.
   *
   * @return array Returns an array containing the HTTP response for the delete supplier request.
   */
  private static function deleteSupplier(): array
  {
    $CLICSHOPPING_Hooks = Registry::get('Hooks');

    $result = $CLICSHOPPING_Hooks->call('Api', 'ApiDeleteSupplier');

    if (empty($result)) {
      $response = ApiShop::notFoundResponse();
    } else {
      $response = ApiShop::HttpResponseOk($result);
    }

    ApiShop::clearCache();

    return $response;
  }

  /**
   * Saves supplier data by invoking the appropriate API call through the Hooks system.
   * Handles the API response and clears cache after processing.
   *
   * @return array An HTTP response array indicating the result of the save operation.
   */
  private static function saveSupplier(): array
  {
    $CLICSHOPPING_Hooks = Registry::get('Hooks');

    $result = $CLICSHOPPING_Hooks->call('Api', 'ApiSaveSupplier');

    if (empty($result)) {
      $response = ApiShop::notFoundResponse();
    } else {
      $response = ApiShop::HttpResponseOk($result);
    }

    ApiShop::clearCache();

    return $response;
  }

  /**
   * Checks the status by querying the database for a specific value.
   *
   * @param string $string The column name to retrieve from the database.
   * @param string $token The session token used to identify the session.
   * @return int The integer value of the specified column from the query result.
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
