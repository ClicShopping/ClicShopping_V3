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
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class securityCheckExtended_mysql_utf8
{
  public $type = 'warning';
  public $has_doc = true;

  public function __construct()
  {
    $CLICSHOPPING_Language = Registry::get('Language');

    $CLICSHOPPING_Language->loadDefinitions('modules/security_check/extended/mysql_utf8', null, null, 'Shop');

    $this->title = CLICSHOPPING::getDef('module_security_check_extended_mysql_utf8_title');
  }

  public function pass()
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qcheck = $CLICSHOPPING_Db->query('show table status');

    if ($Qcheck->fetch() !== false) {
      do {
        if ($Qcheck->hasValue('Collation') && ($Qcheck->value('Collation') != 'utf8mb4_unicode_ci')) {
          return false;
        }
      } while ($Qcheck->fetch());
    }

    return true;
  }

  public function getMessage()
  {
    return HTML::link(CLICSHOPPING::link(null, 'A&Tools\DataBaseTables&DataBaseTables'), CLICSHOPPING::getDef('module_security_check_extended_mysql_utf8_error'));
  }
}
