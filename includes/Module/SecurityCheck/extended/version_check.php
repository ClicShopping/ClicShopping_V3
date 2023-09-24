<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\Cache;
use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class securityCheckExtended_version_check
{
  public $type = 'warning';
  public $has_doc = true;

  public function __construct()
  {
    $CLICSHOPPING_Language = Registry::get('Language');

    $CLICSHOPPING_Language->loadDefinitions('modules/security_check/extended/version_check', null, null, 'Shop');

    $this->title = CLICSHOPPING::getDef('module_security_check_extended_version_check_title');
  }

  public function pass()
  {
    $VersionCache = new Cache('clicshopping_version_check');

    return $VersionCache->exists() && ($VersionCache->getTime() > strtotime('-30 days'));
  }

  public function getMessage()
  {
    return HTML::link(CLICSHOPPING::link(null, 'A&Tools\Upgrade&Upgrade'), CLICSHOPPING::getDef('module_security_check_extended_version_check_error'));
  }
}