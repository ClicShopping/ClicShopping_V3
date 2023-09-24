<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Api\Sites\Shop\Pages\Category;

use ClicShopping\Apps\Configuration\Api\Classes\Shop\ApiShop;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class Category extends \ClicShopping\OM\PagesAbstract
{
  protected $file = null;
  protected bool $use_site_template = false;
  protected mixed $lang;
  protected mixed $Db;

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
        $check = $this->statusCheck('get_categories_status', $token);

        if (empty($result) || $check == 0) {
          $response = ApiShop::notFoundResponse();
          Registry::get('Session')->kill();
        } else {
          $response = static::getCategories();
        }
        break;
      case 'DELETE':
        $token = HTML::sanitize($_GET['token']);
        $result = ApiShop::checkToken($token);
        $check = $this->statusCheck('delete_categories_status', $token);

        if (empty($result) || $check == 0) {
          $response = ApiShop::notFoundResponse();
          Registry::get('Session')->kill();
        } else {
          $response = static::deleteCategories();
        }
        break;
      case 'POST':
        $token = HTML::sanitize($_GET['token']);
        $result = ApiShop::checkToken($token);

        if (isset($_GET['update'])) {
          $check = $this->statusCheck('update_categories_status', $token);

          if (empty($result) || $check == 0) {
            $response = ApiShop::notFoundResponse();
            Registry::get('Session')->kill();
          } else {
            $response = static::saveCategories();
          }
        } elseif (isset($_GET['update'])) {
          $check = $this->statusCheck('insert_categories_status', $token);

          if (empty($result) || $check == 0) {
            $response = ApiShop::notFoundResponse();
            Registry::get('Session')->kill();
          } else {
            $response = static::saveCategories();
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
   * @return array
   */
  private static function getCategories(): array
  {
    $CLICSHOPPING_Hooks = Registry::get('Hooks');

    $result = $CLICSHOPPING_Hooks->call('Api', 'ApiGetCategories');

    if (empty($result)) {
      $response = ApiShop::notFoundResponse();
    } else {
      $response = ApiShop::HttpResponseOk($result);
    }

    ApiShop::clearCache();

    return $response;
  }

  /**
   * @param int|null $id
   * @param int|string $language_id
   * @return array
   */
  private static function deleteCategories(): array
  {
    $CLICSHOPPING_Hooks = Registry::get('Hooks');

    $result = $CLICSHOPPING_Hooks->call('Api', 'ApiDeleteCategories');

    if (empty($result)) {
      $response = ApiShop::notFoundResponse();
    } else {
      $response = ApiShop::HttpResponseOk($result);
    }

    ApiShop::clearCache();

    return $response;
  }

  /**
   * @return array
   */
  private static function saveCategories(): array
  {
    $CLICSHOPPING_Hooks = Registry::get('Hooks');

    $result = $CLICSHOPPING_Hooks->call('Api', 'ApiSaveCategories');

    if (empty($result)) {
      $response = ApiShop::notFoundResponse();
    } else {
      $response = ApiShop::HttpResponseOk($result);
    }

    ApiShop::clearCache();

    return $response;
  }

  /**
   * @param string $string
   * @param string $token
   * @return int
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
