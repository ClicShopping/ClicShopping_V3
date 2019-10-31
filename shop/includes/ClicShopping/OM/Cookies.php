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

  class Cookies
  {
    protected $domain;
    protected $path;

    protected $name;
    protected $value;
    protected $expire;
    protected $secure;
    protected $httpOnly;
    protected $sameSite;

    public function __construct()
    {
      $this->domain = CLICSHOPPING::getConfig('http_cookie_domain');
      $this->path = CLICSHOPPING::getConfig('http_cookie_path');
      $this->sameSite = 'Lax';
    }

    /**
     * @param string $name
     * @param string|null $value
     * @param int $expire
     * @param string|null $path
     * @param string|null $domain
     * @param bool $secure
     * @param bool $httponly
     * @param string|null $sameSite
     * @return string
     */
    public function set(string $name, ?string $value = '', int $expire = 0, ?string $path = null, ?string $domain = null, bool $secure = true, bool $httponly = true,  ?string $sameSite = 'Lax'): string
    {
      return setcookie($name, $value, $expire, isset($path) ? $path : $this->path, isset($domain) ? $domain : $this->domain, $secure, $httponly, isset($sameSite) ? $sameSite : $this->sameSite);
    }

    /**
     * @param string $name
     * @param string|null $path
     * @param string|null $domain
     * @param bool $secure
     * @param bool $httponly
     * @param string|null $sameSite
     * @return bool
     */
    public function del(string $name, ?string $path = null, ?string $domain = null, bool $secure = true, bool $httponly = true, ?string $sameSite = null): bool
    {
      if ($this->set($name, '', time() - 3600, $path, $domain, $secure, $httponly, $sameSite)) {
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

    /**
     * Gets the SameSite attribute.
     *
     * @param string $same_site
     * @return string|null
     */
    public function setSameSite(?string $same_site): ?string
    {
      $this->sameSite = $same_site;
    }


    /**
     * @return string
     */
    public function getSameSite(): string
    {
      return $this->sameSite;
    }
  }
