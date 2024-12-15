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

use Exception;
use GuzzleHttp\Client as GuzzleClient;
use function count;
use function in_array;
use function strlen;
use const JSON_PRETTY_PRINT;

/**
 * The HTTP class provides a collection of static methods to handle HTTP requests, responses,
 * redirections, client IP retrieval, and security configurations such as HSTS.
 *
 * It utilizes the GuzzleHttp client for handling requests and provides utility methods
 * to manipulate HTTP headers and retrieve domain or IP-related information.
 */
class HTTP
{
  protected static string $request_type;

  /**
   * Determines and sets the type of the current request (SSL or NONSSL) based on server environment variables.
   *
   * @return void
   */
  public static function setRequestType()
  {
    static::$request_type = ((isset($_SERVER['HTTPS']) && (mb_strtolower($_SERVER['HTTPS']) == 'on')) || (isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] == 443))) ? 'SSL' : 'NONSSL';
  }

  /**
   * Retrieves the current request type.
   *
   * @return string The type of the request.
   */
  public static function getRequestType(): string
  {
    return static::$request_type;
  }

  /*
   * Use HTTP Strict Transport Security to force client to use secure connections only
   */
  /**
   * Handles HTTP Strict Transport Security (HSTS) for secure connections.
   *
   * @param bool $use_sts Determines whether to send HSTS headers or redirect to HTTPS.
   *                       If true, the method sends the HSTS header. If false, it redirects to HTTPS and terminates further execution.
   * @return void
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
   * Redirects the browser to the specified URL with an optional HTTP response code.
   *
   * @param string|null $url The URL to redirect to. It can be null.
   * @param int $http_response_code Optional HTTP response status code for the redirection. Defaults to 0.
   * @return void
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
   * Sends an HTTP request based on the provided data and retrieves the response.
   *
   * @param array $data An associative array containing the following keys:
   *                    - 'header' (array): Optional. An array of request headers.
   *                    - 'parameters' (mixed): Optional. Parameters to be sent with the request.
   *                    - 'method' (string): Optional. HTTP method to use ('get' or 'post').
   *                    - 'cafile' (string): Optional. Path to the certificate authority file for SSL validation.
   *                    - 'format' (string): Optional. Expected response format, e.g., 'json'.
   *                    - 'url' (string): Required. The URL for the request.
   *                    - 'certificate' (string): Optional. Path to the certificate file for SSL authentication.
   * @return mixed The response body. If 'format' is set to 'json', the response will be decoded into an array. Returns false if an error occurs.
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
   * Sets the HTTP response code for the current execution context.
   *
   * @param int $code The HTTP response code to be set.
   * @return bool Returns true if the response code is successfully set. Throws an exception and returns false if the headers are already sent.
   */

  public static function setResponseCode(int $code): bool
  {
    if (headers_sent()) {
      throw new InvalidArgumentException('HTTP::setResponseCode() - headers already sent, cannot set response code.');

      return false;
    }

    http_response_code($code);

    return true;
  }

  /**
   * Retrieves the IP address of the client making the request.
   * Optionally, it can return the IP in its integer representation.
   *
   * @param bool $to_int Indicates whether the IP address should be returned as an integer.
   *                      If true, the IP address is converted to an unsigned integer.
   *                      Defaults to false.
   *
   * @return string The IP address of the client. Returns "0.0.0.0" if no valid IP address is found.
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
  /**
   * Retrieves the provider name for the customer based on their IP address.
   *
   * This method attempts to resolve the remote client's IP address to a hostname
   * and constructs a name based on the first two segments of the resolved hostname.
   * If the IP is local or cannot be resolved, it returns 'Unknown or localhost'.
   *
   * @return string The provider name constructed from the resolved hostname, or 'Unknown or localhost'.
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
   * Determines the URL domain based on the current site type.
   *
   * @return string Returns the full domain URL for either the admin panel or the shop, depending on the site context.
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
   * Retrieves the shop's URL domain by combining the HTTP server and HTTP path configurations.
   *
   * @return string The constructed shop URL domain.
   */
  public static function getShopUrlDomain(): string
  {
    $domain = CLICSHOPPING::getConfig('http_server', 'Shop') . CLICSHOPPING::getConfig('http_path', 'Shop');

    return $domain;
  }

  /**
   * Retrieves the URI from the server request, removing any OpenID-related query string parameters.
   *
   * @return string The sanitized URI without OpenID-related parameters.
   */
  public static function getUri(): string
  {
    $uri = rtrim(preg_replace('#((?<=\?)|&)openid\.[^&]+#', '', $_SERVER['REQUEST_URI']), '?');

    return $uri;
  }

  /**
   * Constructs and returns the full normalized path based on the given input, separator, and system root configurations.
   *
   * @param string $path The relative or absolute path to be processed. Defaults to an empty string.
   * @param string $separator The directory separator to use for path normalization. Defaults to '/'.
   * @return string The fully resolved and normalized path.
   */
  public static function getFullPath(string $path = '', string $separator = '/'): string
  {
    $systemroot = CLICSHOPPING::getSite('Shop');

    $base = CLICSHOPPING::getSite('Shop');

    // Normalize system root and base paths
    $systemroot = rtrim($systemroot, $separator) . $separator;
    $base = rtrim($base, $separator) . $separator;

    if ($path === '' || $path === '.' . $separator) {
      return $base;
    }

    if (substr($path, 0, 3) === '..' . $separator) {
      $path = $base . $path;
    }

    // Normalize path
    $path = rtrim($path, $separator) . $separator;

    // Absolute path
    if ($path[0] === $separator || strpos($path, $systemroot) === 0) {
      return $path;
    }

    // Relative path from 'Here'
    if (substr($path, 0, 2) === '.' . $separator || $path[0] !== '.') {
      $arrn = preg_split('/\\' . $separator . '/', $path, -1, PREG_SPLIT_NO_EMPTY);
      if ($arrn[0] !== '.') {
        array_unshift($arrn, '.');
      }
      $arrn[0] = rtrim($base, $separator);
      return join($separator, $arrn);
    }

    return $path;
  }
}