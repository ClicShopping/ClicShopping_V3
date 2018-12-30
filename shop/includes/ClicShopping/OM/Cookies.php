<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\OM;

  use ClicShopping\OM\CLICSHOPPING;

  class Cookies  {
    protected $domain;
    protected $path;

    public function __construct() {
        $this->domain = CLICSHOPPING::getConfig('http_cookie_domain');
        $this->path = CLICSHOPPING::getConfig('http_cookie_path');
    }

    public function set($name, $value = '', $expire = 0, $path = null, $domain = null, $secure = true, $httponly = true) {
      return setcookie($name, $value, $expire, isset($path) ? $path : $this->path, isset($domain) ? $domain : $this->domain, $secure, $httponly);
    }

    public function del($name, $path = null, $domain = null, $secure = true, $httponly = true)  {
      if ($this->set($name, '', time() - 3600, $path, $domain, $secure, $httponly)) {
        if (isset($_COOKIE[$name])) {
            unset($_COOKIE[$name]);
        }

        return true;
      }

      return false;
    }

    public function getDomain() {
        return $this->domain;
    }

    public function getPath() {
        return $this->path;
    }

    public function setDomain($domain) {
        $this->domain = $domain;
    }

    public function setPath($path) {
        $this->path = $path;
    }
  }
