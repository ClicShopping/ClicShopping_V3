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
 * This class performs an extended security check for the presence of directory
 * listing in the 'includes/' directory. It validates file listing visibility
 * and ensures the server does not expose unnecessary details.
 */
class securityCheckExtended_includes_directory_listing
{
  public $type = 'warning';
  public $has_doc = true;

  /**
   *
   * @return void
   */
  public function _construct()
  {
    $CLICSHOPPING_Language = Registry::get('Language');

    $CLICSHOPPING_Language->loadDefinitions('modules/security_check/extended/includes_directory_listing', null, null, 'Shop');

    $this->title = CLICSHOPPING::getDef('module_security_check_extended_includes_directory_listing_title');
  }

  /**
   * Validates the HTTP response code of a request to a specific URL.
   *
   * @return bool Returns true if the HTTP response code is not 200, otherwise false.
   */
  public function pass()
  {
    $request = $this->getHttpRequest(CLICSHOPPING::link('Shop/includes/'));

    return $request['http_code'] != 200;
  }

  /**
   * Retrieves a predefined message string from the CLICSHOPPING definitions.
   *
   * @return string The corresponding message string defined in the system.
   */
  public function getMessage()
  {
    return CLICSHOPPING::getDef('module_security_check_extended_includes_directory_listing_http_200');
  }

  /**
   * Sends an HTTP HEAD request to the given URL and returns information about the request.
   *
   * @param string $url The URL to which the HTTP HEAD request should be sent.
   * @return mixed Information about the HTTP request, or 'error' if the request fails.
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