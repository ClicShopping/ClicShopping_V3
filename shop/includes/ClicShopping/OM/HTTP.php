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

  use ClicShopping\OM\Is;

  use GuzzleHttp\Client as GuzzleClient;

  class HTTP
  {
    protected static $request_type;

    public static function setRequestType()
    {
      static::$request_type = ((isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on')) || (isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] == 443))) ? 'SSL' : 'NONSSL';
    }

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
     * @param $url
     * @param null $http_response_code - 301 - 302 - 303 - 307
     */

    public static function redirect(string $url, ?string $http_response_code = null)
    {

      if ((strstr($url, "\n") === false) && (strstr($url, "\r") === false)) {
        if (strpos($url, '&amp;') !== false) {
          $url = str_replace('&amp;', '&', $url);
        }

        header('Location: ' . $url, true, $http_response_code);
      }

      exit;
    }

    /**
     * @param array $parameters url, headers, parameters, method, verify_ssl, cafile, certificate, proxy
     */
    public static function getResponse(array $data)
    {

      if (!isset($data['header']) || !is_array($data['header'])) {
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
          if (!isset($options['headers']) || !isset($options['headers']['Content-Type'])) {
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
      } catch (\Exception $e) {
        $json = json_encode([
          'method' => $data['method'],
          'url' => $data['url'],
          'options' => $options
        ], \JSON_PRETTY_PRINT);

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
     * @param string $ip , th ip of the client
     * @access public
     *
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
     * @access public
     */
    public static function getProviderNameCustomer(): string
    {
      if (!empty($_SERVER["REMOTE_ADDR"]) && $_SERVER["REMOTE_ADDR"] != '::1') { //check ip from share internet
        $provider_client_ip = gethostbyaddr($_SERVER["REMOTE_ADDR"]);
        $str = preg_split("/\./", $provider_client_ip);
        $i = count($str);
        $x = $i - 1;
        $n = $i - 2;
        $isp_provider_client = $str[$n] . '.' . $str[$x];

        return $isp_provider_client;
      } else {
        return 'Unkown or localhost';
      }
    }


    /**
     *
     * public function
     * @param string  type of HTTP of domain
     * @return $domain, type of HTTP of domain
     *
     */
    public static function typeUrlDomain(): string
    {
      if (CLICSHOPPING::getSite() == 'ClicShoppingAdmin') {
        $domain = CLICSHOPPING::getConfig('http_server', 'ClicShoppingAdmin') . CLICSHOPPING::getConfig('http_path', 'ClicShoppingAdmin');
      } else {
        $domain = CLICSHOPPING::getConfig('http_server', 'Shop') . CLICSHOPPING::getConfig('http_path', 'Shop');
      }

      return $domain;
    }

    /**
     *
     * public function
     * @param string  type of HTTP of domain
     * @return $domain, type of HTTP of domain
     *
     */
    public static function getShopUrlDomain(): string
    {
      $domain = CLICSHOPPING::getConfig('http_server', 'Shop') . CLICSHOPPING::getConfig('http_path', 'Shop');

      return $domain;
    }
  }
