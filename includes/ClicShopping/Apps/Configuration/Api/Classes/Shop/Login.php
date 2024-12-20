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

  /**
   * Constructor method.
   *
   * @param string $username The username of the user.
   * @param string $key The key associated with the user.
   * @param string|null $ip The IP address of the user (optional).
   * @return void
   */
  public function __construct(string $username, string $key, ?string $ip)
  {
    $this->username = $username;
    $this->key = $key;
    $this->ip = $ip;
    $this->lang = Registry::get('Language');
    /**
     *
     */
      $this->Db = Registry::get('Db');
  }

  /**
   * Handles user login by authenticating username, key, and IP, then generating a session token.
   *
   * This method checks if the API module is active and processes login credentials.
   * If valid credentials are provided, it checks IP restrictions and generates a session token.
   * If invalid credentials are detected or the IP check fails, it returns appropriate error messages.
   *
   * @return string|false Returns a session token string upon successful login,
   *                      'bad IP' if the IP is not allowed, 'no access' for invalid credentials,
   *                      or false if the API module is inactive.
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