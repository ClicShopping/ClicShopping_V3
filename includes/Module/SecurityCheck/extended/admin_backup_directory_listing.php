<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class securityCheckExtended_admin_backup_directory_listing
{
  public $type = 'danger';
  public $has_doc = true;

  public function __construct()
  {
    $CLICSHOPPING_Language = Registry::get('Language');

    $CLICSHOPPING_Language->loadDefinitions('modules/security_check/extended/admin_backup_directory_listing', null, null, 'Shop');

    $this->title = CLICSHOPPING::getDef('module_security_check_extended_admin_backup_directory_listing_title');
  }

  public function pass()
  {
    $request = $this->getHttpRequest(CLICSHOPPING::link('Shop/includes/ClicShopping/Work/Backups/'));

    return $request['http_code'] != 200;
  }

  public function getMessage()
  {
    return CLICSHOPPING::getDef('module_security_check_extended_admin_backup_directory_listing_http_200', [
        'backups_url' => CLICSHOPPING::link('Shop/includes/ClicShopping/Work/Backups/'),
        'backups_path' => CLICSHOPPING::getConfig('http_path', 'Shop') . 'includes/ClicShopping/Work/Backups/'
      ]
    );
  }

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

    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
      curl_setopt($curl, CURLOPT_USERPWD, $_SERVER['PHP_AUTH_USER'] . ':' . $_SERVER['PHP_AUTH_PW']);

      $this->type = 'warning';
    }

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

