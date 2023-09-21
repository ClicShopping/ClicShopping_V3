<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM;

abstract class SessionAbstract
{
  protected $name;
  protected bool $force_cookies = true;
  public ?string $sameSite;

  /**
   * Checks if a session exists
   *
   * @param string $session_id The ID of the session
   */

  abstract public function exists(string $session_id);

  /**
   * Verify an existing session ID and create or resume the session if the existing session ID is valid
   *
   * @return boolean
   */

  public function start(): bool
  {
    $CLICSHOPPING_Cookies = Registry::get('Cookies');

// this class handles session.use_strict_mode already
    if ((int)ini_get('session.use_strict_mode') === 1) {
      ini_set('session.use_strict_mode', 0);
    }

    if (parse_url(CLICSHOPPING::getConfig('http_server'), PHP_URL_SCHEME) == 'https') {
      if ((int)ini_get('session.cookie_secure') === 0) {
        ini_set('session.cookie_secure', 1);
      }
    }

    if ((int)ini_get('session.cookie_httponly') === 0) {
      ini_set('session.cookie_httponly', 1);
    }

    if ((int)ini_get('session.use_only_cookies') !== 1) {
      ini_set('session.use_only_cookies', 1);
    }

    $session_can_start = true;

    Registry::get('Hooks')->call('Session', 'StartBefore', [
      'can_start' => &$session_can_start
    ]);

    $options = [
      'lifetime' => 0,             // The lifetime of the cookie in seconds.
      'path' => $CLICSHOPPING_Cookies->getPath(),               // The path where information is stored.
      'domain' => $CLICSHOPPING_Cookies->getDomain(),   // The domain of the cookie.
      'secure' => (bool)ini_get('session.cookie_secure'),            // The cookie should only be sent over secure connections.
      'httponly' => (bool)ini_get('session.cookie_httponly'),          // The cookie can only be accessed through the HTTP protocol.
      'samesite' => $CLICSHOPPING_Cookies->getSameSite() //"Lax/Strict"  // The cookie can only be accessed if it was initiated from the same registrable domain or LAx
    ];

    session_set_cookie_params($options);

    if (isset($_GET[$this->name]) && ($this->force_cookies || !(bool)preg_match('/^[a-zA-Z0-9,-]+$/', $_GET[$this->name]) || !$this->exists($_GET[$this->name]))) {
      unset($_GET[$this->name]);
    }

    if (isset($_POST[$this->name]) && ($this->force_cookies || !(bool)preg_match('/^[a-zA-Z0-9,-]+$/', $_POST[$this->name]) || !$this->exists($_POST[$this->name]))) {
      unset($_POST[$this->name]);
    }

    if (isset($_COOKIE[$this->name]) && (!(bool)preg_match('/^[a-zA-Z0-9,-]+$/', $_COOKIE[$this->name]) || !$this->exists($_COOKIE[$this->name]))) {
      $CLICSHOPPING_Cookies->del($this->name, $CLICSHOPPING_Cookies->getPath(), $CLICSHOPPING_Cookies->getDomain(), (bool)ini_get('session.cookie_secure'), (bool)ini_get('session.cookie_httponly'));
    }

    if ($this->force_cookies === false) {
      if (isset($_GET[$this->name]) && (!isset($_COOKIE[$this->name]) || ($_COOKIE[$this->name] != $_GET[$this->name]))) {
        session_id($_GET[$this->name]);
      } elseif (isset($_POST[$this->name]) && (!isset($_COOKIE[$this->name]) || ($_COOKIE[$this->name] != $_POST[$this->name]))) {
        session_id($_POST[$this->name]);
      }
    }

    if (($session_can_start === true) && session_start()) {
      Registry::get('Hooks')->call('Session', 'StartAfter');

      return true;
    }

    return false;
  }

  /**
   * @param bool $force_cookies
   */
  public function setForceCookies(bool $force_cookies)
  {
    $this->force_cookies = $force_cookies;
  }

  /**
   * @return bool
   */
  public function isForceCookies(): bool
  {
    return $this->force_cookies;
  }

  /**
   * Checks if the session has been started or not
   * @return boolean
   */

  public function hasStarted(): bool
  {
    return session_status() === PHP_SESSION_ACTIVE;
  }

  /**
   * Deletes an existing session
   * @return bool
   */
  public function kill(): bool
  {
    $CLICSHOPPING_Cookies = Registry::get('Cookies');

    $result = true;

    if (isset($_COOKIE[$this->name])) {
      $CLICSHOPPING_Cookies->del($this->name, $CLICSHOPPING_Cookies->getPath(), $CLICSHOPPING_Cookies->getDomain(), (bool)ini_get('session.cookie_secure'), (bool)ini_get('session.cookie_httponly'), $CLICSHOPPING_Cookies->getSameSite('Lax'));
    }

    if ($this->hasStarted()) {
      $_SESSION = [];

      $result = session_destroy();
    }

    return $result;
  }

  /**
   * Delete an existing session and move the session data to a new session with a new session ID
   * @return bool
   */
  public function recreate(): bool
  {
    $delete_flag = true;

    if (!$this->exists(session_id())) {
      $delete_flag = false;
    }

    $session_old_id = session_id();

    $result = session_regenerate_id($delete_flag);

    if ($result === true) {
      Registry::get('Hooks')->call('Session', 'Recreated', [
        'old_id' => $session_old_id
      ]);

      return true;
    }

    return false;
  }

  /**
   * Sets the name of the session
   *
   * @param string $name The name of the session
   */

  public function setName(string $name): string
  {
    return session_name($name);
  }

  /**
   * Sets the life time of the session (in seconds)
   * @param int $time The life time of the session (in seconds)
   */

  public function setLifeTime(float $time): float
  {
    return ini_set('session.gc_maxlifetime', $time);
  }

  /**
   * @return string|null
   */
  public function getName(): ?string
  {
    return $this->name;
  }

  /**
   * Gets the SameSite attribute.
   *
   * @return string|null
   */
  public function getSameSite(): ?string
  {
    return $this->sameSite;
  }
}
