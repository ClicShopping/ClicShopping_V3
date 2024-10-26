<?php


namespace ClicShopping\Apps\Configuration\Api\Classes\Shop;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class Login
{
  private mixed $lang;
  private mixed $db;
  public string $username;
  public string $key;
  public ?string $ip;

  public function __construct(string $username, string $key, ?string $ip)
  {
    $this->username = $username;
    $this->key = $key;
    $this->ip = $ip;
    $this->lang = Registry::get('Language');
    $this->Db = Registry::get('Db');
  }

  /**
   * @return false
   */
  public function getLogin()
  {
    if (!\defined('CLICSHOPPING_APP_API_AI_STATUS') && CLICSHOPPING_APP_API_AI_STATUS == 'False') {
      return false;
    }

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
    $result = $this->authentification->checkAccess();

    if (isset($result) && $result !== false) {
      $api_id = $result['api_id'];

      if ($this->authentification->getIps($api_id) === true) {
        $token = $this->authentification->addSession($api_id);
      } else {
        $token = 'bad IP';
      }
    } else {
      $token = 'no access';
    }

    return $token;
  }
}