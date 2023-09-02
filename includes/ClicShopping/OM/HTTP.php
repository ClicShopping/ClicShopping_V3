<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM;

use Exception;
use GuzzleHttp\Client as GuzzleClient;
use function count;
use function in_array;
use function strlen;
use const JSON_PRETTY_PRINT;

class HTTP
{
  protected static string $request_type;

  public static function setRequestType()
  {
    static::$request_type = ((isset($_SERVER['HTTPS']) && (mb_strtolower($_SERVER['HTTPS']) == 'on')) || (isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] == 443))) ? 'SSL' : 'NONSSL';
  }

  /**
   * @return string
   */
  public static function getRequestType(): string
  {
    return static::$request_type;
  }

  /*
   * Use HTTP Strict Transport Security to force client to use secure connections only
   */
  public static function getHSTS(bool $use_sts = true)
  {
    if (static::$request_type == 'SSL' && isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
      if ($use_sts === true) {
        header('Strict-Transport-Security: max-age=500; includeSubDomains; preload');
      } else {
        header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true, 301);
// we are in cleartext at the moment, prevent further execution and output
        die();
      }
    }
  }

  /**
   * @param string|null $url
   * @param int $http_response_code - 301 - 302 - 303 - 307
   */

  public static function redirect(?string $url, int $http_response_code = 0)
  {
    if ((strstr($url, "\n") === false) && (strstr($url, "\r") === false)) {
      if (str_contains($url, '&amp;')) {
        $url = str_replace('&amp;', '&', $url);
      }

      header('Location: ' . $url, true, $http_response_code);
    }

    exit;
  }

  /**
   * @param array $data url, headers, parameters, method, verify_ssl, cafile, certificate, proxy
   */
  public static function getResponse(array $data)
  {
    if (!isset($data['header']) || !\is_array($data['header'])) {
      $data['header'] = [];
    }

    if (!isset($data['parameters'])) {
      $data['parameters'] = '';
    }

    if (!isset($data['method'])) {
      $data['method'] = !empty($data['parameters']) ? 'post' : 'get';
    }

    if (!isset($data['cafile'])) {
      $data['cafile'] = CLICSHOPPING::BASE_DIR . 'External/cacert.pem';
    }

    if (isset($data['format']) && !in_array($data['format'], ['json'])) {
      trigger_error('HttpRequest::getResponse(): Unknown "format": ' . $data['format']);

      unset($data['format']);
    }

    $options = [];

    if (!empty($data['header'])) {
      foreach ($data['header'] as $h) {
        [$key, $value] = explode(':', $h, 2);

        $options['headers'][$key] = $value;

        unset($key);
        unset($value);
      }
    }

    if (isset($data['format']) && ($data['format'] === 'json')) {
      $options['json'] = $data['parameters'];
    } else {
      if (($data['method'] === 'post') && !empty($data['parameters'])) {
        if (!isset($options['headers'], $options['headers']['Content-Type'])) {
          $options['headers']['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        $options['body'] = $data['parameters'];
      }
    }

    if (isset($data['cafile']) && is_file($data['cafile'])) {
      $options['verify'] = $data['cafile'];
    }

    if (isset($data['certificate']) && is_file($data['certificate'])) {
      $options['cert'] = $data['certificate'];
    }

    $result = false;

    try {
      $client = new GuzzleClient();
      $response = $client->request($data['method'], $data['url'], $options);

      $result = $response->getBody()->getContents();

      if (isset($data['format']) && ($data['format'] === 'json')) {
        $result = json_decode($result, true);
      }
    } catch (Exception $e) {
      $json = json_encode([
        'method' => $data['method'],
        'url' => $data['url'],
        'options' => $options
      ], JSON_PRETTY_PRINT);

      if ($json !== false) {
        trigger_error($json);
      }

      trigger_error($e->getMessage());
    }

    return $result;
  }

  /**
   * Set the HTTP status code
   *
   * @param int $code The HTTP status code to set
   * @return boolean
   */

  public static function setResponseCode(int $code): bool
  {
    if (headers_sent()) {
      trigger_error('HTTP::setResponseCode() - headers already sent, cannot set response code.', E_USER_ERROR);

      return false;
    }

    http_response_code($code);

    return true;
  }

  /**
   * Get the IP address of the client
   * @param bool $to_int
   * @return string
   */

  public static function getIpAddress(bool $to_int = false): string
  {
    $ips = [];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      foreach (array_reverse(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])) as $x_ip) {
        $ips[] = trim($x_ip);
      }
    }
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
      $ips[] = trim($_SERVER['HTTP_CLIENT_IP']);
    }
    if (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
      $ips[] = trim($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']);
    }
    if (isset($_SERVER['HTTP_PROXY_USER'])) {
      $ips[] = trim($_SERVER['HTTP_PROXY_USER']);
    }
    if (isset($_SERVER['REMOTE_ADDR'])) {
      $ips[] = trim($_SERVER['REMOTE_ADDR']);
    }

    $ip = '0.0.0.0';

    foreach ($ips as $req_ip) {
      if (Is::IpAddress($req_ip)) {
        $ip = $req_ip;
        break;
      }
    }

    if ($to_int === true) {
      $ip = sprintf('%u', ip2long($ip));
    }

    return $ip;
  }

  /*
   * Get the provider name of the client
   * $isp_provider_client the provider name
   * return string
   */
  public static function getProviderNameCustomer(): string
  {
    if (!empty($_SERVER["REMOTE_ADDR"]) && $_SERVER["REMOTE_ADDR"] != '::1') { //check ip from share internet
      $provider_client_ip = gethostbyaddr($_SERVER["REMOTE_ADDR"]);
      $str = preg_split("/\./", $provider_client_ip);
      $i = count($str);

      $x = $str[0];

      if ($i > 1) {
        $n = $str[1];
      } else {
        $n = '';
      }

      return $x . '.' . $n;
    } else {
      return 'Unkown or localhost';
    }
  }

  /**
   * @return string
   */
  public static function typeUrlDomain(): string
  {
    if (CLICSHOPPING::getSite() === 'ClicShoppingAdmin') {
      $domain = CLICSHOPPING::getConfig('http_server', 'ClicShoppingAdmin') . CLICSHOPPING::getConfig('http_path', 'ClicShoppingAdmin');
    } else {
      $domain = static::getShopUrlDomain();
    }

    return $domain;
  }

  /**
   * @return string
   */
  public static function getShopUrlDomain(): string
  {
    $domain = CLICSHOPPING::getConfig('http_server', 'Shop') . CLICSHOPPING::getConfig('http_path', 'Shop');

    return $domain;
  }

  /**
   * get the url requests
   * @return string
   */
  public static function getUri(): string
  {
    $uri = rtrim(preg_replace('#((?<=\?)|&)openid\.[^&]+#', '', $_SERVER['REQUEST_URI']), '?');

    return $uri;
  }

  /**
   * Resolve relative / (Unix-like)absolute path
   * @param string $path target path
   * @param string $separator separator
   * @return string
   */
  public static function getFullPath(string $path = '', string $separator = '/'): string
  {
    $systemroot = CLICSHOPPING::getSite('Shop');

    $base = CLICSHOPPING::getSite('Shop');

    if ($base[0] === $separator && substr($base, 0, strlen($systemroot)) !== $systemroot) {
      $base = $systemroot . substr($base, 1);
    }
    if ($base !== $systemroot) {
      $base = rtrim($base, $separator);
    }

    if ($path == '' || $path == '.' . $separator) {
      return $base;
    }

    if (substr($path, 0, 3) == '..' . $separator) {
      $path = $base . $separator . $path;
    }

    if ($path !== $systemroot) {
      $path = rtrim($path, $separator);
    }

    // Absolute path
    if ($path[0] === $separator || strpos($path, $systemroot) === 0) {
      return $path;
    }

    // Relative path from 'Here'
    if (substr($path, 0, 2) == '.' . $separator || $path[0] !== '.') {
      $arrn = preg_split($preg_separator, $path, -1, PREG_SPLIT_NO_EMPTY);
      if ($arrn[0] !== '.') {
        array_unshift($arrn, '.');
      }
      $arrn[0] = rtrim($base, $separator);
      return join($separator, $arrn);
    }

    return $path;
  }
}