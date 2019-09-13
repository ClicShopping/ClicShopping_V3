<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\OM;

  use ClicShopping\OM\CLICSHOPPING;

  class Cookies
  {
    protected $domain;
    protected $path;

    public function __construct()
    {
      $this->domain = CLICSHOPPING::getConfig('http_cookie_domain');
      $this->path = CLICSHOPPING::getConfig('http_cookie_path');
    }

    /**
     * @param string $name
     * @param string|null $value
     * @param int $expire
     * @param string|null $path
     * @param string|null $domain
     * @param bool $secure
     * @param bool $httponly
     * @return string
     */
    public function set(string $name, ?string $value = '', int $expire = 0, ?string $path = null, ?string $domain = null, bool $secure = true, bool $httponly = true): string
    {
      return setcookie($name, $value, $expire, isset($path) ? $path : $this->path, isset($domain) ? $domain : $this->domain, $secure, $httponly);
    }

    /**
     * @param tring $name
     * @param string|null $path
     * @param string|null $domain
     * @param bool $secure
     * @param bool $httponly
     * @return bool
     */
    public function del(tring $name, ?string $path = null, ?string $domain = null, bool $secure = true, $httponly = true): bool
    {
      if ($this->set($name, '', time() - 3600, $path, $domain, $secure, $httponly)) {
        if (isset($_COOKIE[$name])) {
          unset($_COOKIE[$name]);
        }

        return true;
      }

      return false;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
      return $this->domain;
    }

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
      return $this->path;
    }

    /**
     * @param string $domain
     * @return string|null
     */
    public function setDomain(string $domain): ?string
    {
      $this->domain = $domain;
    }

    /**
     * @param string|null $path
     * @return string|null
     */
    public function setPath(?string $path): ?string
    {
      $this->path = $path;
    }
  }
