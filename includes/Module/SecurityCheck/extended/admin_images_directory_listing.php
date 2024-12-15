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
 * This class performs a security check to ensure that the admin images directory
 * does not allow directory listing or returns an HTTP 200 status code. It conducts
 * an HTTP request to the specified directory and verifies the response code.
 */
class securityCheckExtended_admin_images_directory_listing
{
  public $type = 'warning';
  public $has_doc = true;

  /**
   * Constructor method for initializing the module's language definitions
   * and setting the title of the security check module.
   *
   * @return void
   */
  public function __construct()
  {
    $CLICSHOPPING_Language = Registry::get('Language');

    $CLICSHOPPING_Language->loadDefinitions('modules/security_check/extended/admin_images_directory_listing', null, null, 'Shop');
    /**
     *
     */
      $this->title = CLICSHOPPING::getDef('module_security_check_extended_admin_images_directory_listing_title');
  }

  /**
   * Checks if the HTTP request to the specified 'images/' link does not return a 200 HTTP status code.
   *
   * @return bool True if the HTTP status code is not 200, false otherwise.
   */
  public function pass()
  {
    $request = $this->getHttpRequest(CLICSHOPPING::link('images/'));

    return $request['http_code'] != 200;
  }

  /**
   * Retrieves the message definition for the HTTP 200 status related to the admin images directory listing.
   *
   * @return string The localized message for the HTTP 200 status.
   */
  public function getMessage()
  {
    return CLICSHOPPING::getDef('module_security_check_extended_admin_images_directory_listing_http_200');
  }

  /**
   * Sends an HTTP HEAD request to the provided URL and retrieves server information.
   *
   * @param string $url The URL to send the HTTP request to.
   * @return array|string Returns an array containing server information if the request is successful, or a string 'error' in case of failure.
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