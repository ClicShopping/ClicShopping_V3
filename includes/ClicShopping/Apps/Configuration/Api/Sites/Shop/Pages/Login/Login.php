<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\Api\Sites\Shop\Pages\Login;

use ClicShopping\Apps\Configuration\Api\Classes\Shop\ApiProducts;
use ClicShopping\Apps\Configuration\Api\Classes\Shop\ApiShop;
use ClicShopping\Apps\Configuration\Api\Classes\Shop\Authentification;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class Login extends \ClicShopping\OM\PagesAbstract
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
    //    echo ApiShop::getheader();
    //     $id = $this->authentification->getUrlId();

// Handle the event
    switch ($requestMethod) {
      case 'POST':
        if (isset($_POST['key'])) {
          $key = HTML::sanitize($_POST['key']);
        } else {
          $key = '';
        }

        if (isset($_POST['username'])) {
          $username = HTML::sanitize($_POST['username']);
        } else {
          $username = '';
        }

        if (isset($_POST['ip'])) {
          $ip = HTML::sanitize($_POST['ip']);
        } else {
          $ip = '';
        }

        Registry::set('Authentification', new Authentification($username, $key, $ip));
        $this->authentification = Registry::get('Authentification');

        $check = $this->authentification->checkUrl('Login');

        if ($check === true) {
          $result = $this->authentification->checkAccess();

          if (isset($result)) {
            $api_id = $result['api_id'];

            if ($this->authentification->getIps($api_id) === true) {
              $_SESSION['api_token'] = $this->authentification->addSession($api_id);
              $response['body'] = $_SESSION['api_token'];
            } else {
              $response['body'] = 'bad IP';
            }
          }
        } else {
          $response['body'] = 'bad token';
        }
        break;
      default:
        $response['body'] = $this->authentification->notFoundResponse();
        break;
    }

    if ($response['body']) {
      //echo $response['body'];
      echo $response['body'];
    }

    exit;
  }
}
