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

/**
 * Class responsible for managing cookies within the application.
 */
class Cookies
{
  protected ?string $domain;
  protected ?string $path;

  protected string $name;
  protected ?string $value;
  protected int $expire;
  protected bool $secure;
  protected bool $httpOnly;
  protected ?string $sameSite;

  /**
   * Constructor method for initializing the class properties.
   *
   * @return void
   */
  public function __construct()
  {
    $this->domain = CLICSHOPPING::getConfig('http_cookie_domain');
    $this->path = CLICSHOPPING::getConfig('http_cookie_path');
    $this->sameSite = 'Lax';
  }

  /**
   * Sets a cookie with the specified parameters.
   *
   * @param string $name The name of the cookie.
   * @param ?string $value The value of the cookie. Default is an empty string.
   * @param int $expire The time the cookie expires. Default is 0.
   * @param ?string $path The path on the server the cookie is available to. Default is null.
   * @param ?string $domain The domain the cookie is available to. Default is null.
   * @param bool $secure Whether the cookie should only be transmitted over a secure HTTPS connection. Default is true.
   * @param bool $httponly Whether the cookie is accessible only through the HTTP protocol. Default is true.
   * @param ?string $sameSite The SameSite attribute of the cookie ("Lax", "Strict", "None"). Default is 'Lax'.
   * @return string Returns the value of the setcookie function.
   */
  public function set(string $name, ?string $value = '', int $expire = 0, ?string $path = null, ?string $domain = null, bool $secure = true, bool $httponly = true, ?string $sameSite = 'Lax'): string
  {
    return setcookie($name, $value, $expire, isset($path) ? $path : $this->path, isset($domain) ? $domain : $this->domain, $secure, $httponly, isset($sameSite) ? $sameSite : $this->sameSite);
  }

  /**
   * Deletes a cookie by setting its expiration time in the past and unsetting it from the $_COOKIE global.
   *
   * @param string $name The name of the cookie to delete.
   * @param string|null $path The path on the server in which the cookie will be available. Defaults to null.
   * @param string|null $domain The (sub)domain that the cookie is available to. Defaults to null.
   * @param bool $secure Indicates if the cookie should only be transmitted over a secure HTTPS connection. Defaults to true.
   * @param bool $httponly When set to true, the cookie will be accessible only through the HTTP protocol. Defaults to true.
   * @param string|null $sameSite The SameSite attribute for the cookie, which can be 'Strict', 'Lax', or 'None'. Defaults to null.
   *
   * @return bool Returns true if the cookie deletion is successful, false otherwise.
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
   * Retrieves the domain.
   *
   * @return string The domain value.
   */
  public function getDomain(): string
  {
    return $this->domain;
  }

  /**
   * Retrieves the current path.
   *
   * @return string|null The current path or null if not set.
   */
  public function getPath(): ?string
  {
    return $this->path;
  }

  /**
   * Sets the domain.
   *
   * @param string $domain The domain to be set.
   * @return string|null The previously set domain, or null if none was set.
   */
  public function setDomain(string $domain): ?string
  {
    $this->domain = $domain;
  }

  /**
   * Sets a new value for the path.
   *
   * @param string|null $path The new value for the path, or null.
   * @return string|null The updated path value, or null if not set.
   */
  public function setPath(?string $path): ?string
  {
    $this->path = $path;
  }

  /**
   * Sets the SameSite attribute value for the cookie.
   *
   * @param string|null $same_site The SameSite attribute value, or null to unset it.
   * @return string|null The previously set SameSite attribute value, or null if none was set.
   */
  public function setSameSite(?string $same_site): ?string
  {
    $this->sameSite = $same_site;
  }


  /**
   * Retrieves the SameSite attribute value.
   *
   * @return string The SameSite attribute.
   */
  public function getSameSite(): string
  {
    return $this->sameSite;
  }
}
