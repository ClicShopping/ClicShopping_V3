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
 * Class that performs a security check to verify if the "admin/includes" directory is correctly secured.
 * It verifies that the directory does not provide a directory listing by checking the HTTP response code.
 */
class securityCheckExtended_admin_includes_directory_listing
{
  public $type = 'warning';
  public $has_doc = true;

  /**
   * Constructor method to initialize the security check module.
   * Loads necessary language definitions and sets the title property.
   *
   * @return void
   */
  public function __construct()
  {
    $CLICSHOPPING_Language = Registry::get('Language');

    $CLICSHOPPING_Language->loadDefinitions('modules/security_check/extended/admin_includes_directory_listing', null, null, 'Shop');
    /**
     *
     */
      $this->title = CLICSHOPPING::getDef('module_security_check_extended_admin_includes_directory_listing_http_200');
  }

  /**
   *
   * @return bool Returns true if the HTTP response code is not 200, otherwise returns false.
   */
  public function pass()
  {
    $request = $this->getHttpRequest(CLICSHOPPING::link('includes/'));

    return $request['http_code'] != 200;
  }

  /**
   * Retrieves the message associated with the security check.
   *
   * @return string The message defined in the context of the module's security check.
   */
  public function getMessage()
  {
    return CLICSHOPPING::getDef('module_security_check_extended_admin_includes_directory_listing_http_200');
  }

  /**
   * Sends an HTTP HEAD request to a given URL and returns information about the request.
   *
   * @param string $url The target URL for the HTTP request.
   * @return mixed An array of information about the HTTP request on success, or the string 'error' if the request fails.
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