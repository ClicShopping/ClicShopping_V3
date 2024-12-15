<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

/**
 * This class performs a security check to ensure the ext/ directory does not expose its contents via directory listing.
 */
class securityCheckExtended_ext_directory_listing
{
  public $type = 'warning';
  public $has_doc = true;

  /**
   * Constructor method for initializing the module.
   *
   * @return void
   */
  public function __construct()
  {
    $CLICSHOPPING_Language = Registry::get('Language');

    $CLICSHOPPING_Language->loadDefinitions('modules/security_check/extended/ext_directory_listing', null, null, 'Shop');

    /**
     *
     */
      $this->title = CLICSHOPPING::getDef('module_security_check_extended_ext_directory_listing_title');
  }

  /**
   * Checks if an HTTP request to a specific URL does not return a 200 status code.
   *
   * @return bool Returns true if the HTTP code is not 200, otherwise false.
   */
  public function pass()
  {
    $request = $this->getHttpRequest(CLICSHOPPING::link('Shop/ext/'));

    return $request['http_code'] != 200;
  }

  /**
   * Retrieves a message definition with dynamic placeholders for the external URL and path.
   *
   * @return string The formatted message string including the external URL and path.
   */
  public function getMessage()
  {
    return CLICSHOPPING::getDef('module_security_check_extended_ext_directory_listing_http_200', [
      'ext_url' => CLICSHOPPING::link('Shop/ext/'),
      'ext_path' => CLICSHOPPING::getConfig('http_path', 'Shop') . 'ext/'
    ]);
  }

  /**
   * Sends a HTTP HEAD request to the specified URL and retrieves information about the request.
   *
   * @param string $url The URL to send the HTTP request to.
   * @return array|string Returns an array of information about the HTTP request if successful, or a string 'error' if the request fails.
   */
  public function getHttpRequest($url)
  {
    $server = parse_url($url);

    if (isset($server['port']) === false) {
      $server['port'] = ($server['scheme'] == 'https') ? 443 : 80;
    }

    if (isset($server['path']) === false) {
      $server['path'] = '/';
    }

    $curl = curl_init($server['scheme'] . '://' . $server['host'] . $server['path'] . (isset($server['query']) ? '?' . $server['query'] : ''));
    curl_setopt($curl, CURLOPT_PORT, $server['port']);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
    curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'HEAD');
    curl_setopt($curl, CURLOPT_NOBODY, true);

    $result = curl_exec($curl);

    if (empty($result)) {
      $info = curl_getinfo($curl);
      curl_close($curl);
    } else {
      $info = 'error';
    }

    return $info;
  }
}