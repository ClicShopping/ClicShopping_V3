<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Service\ClicShoppingAdmin;

use ClicShopping\OM\Registry;
use ClicShopping\Sites\ClicShoppingAdmin\CfgModulesAdmin as CfgModulesAdminClass;
/**
 * The CfgModulesAdmin service manages the initialization and termination process
 * of the CfgModulesAdmin class within the ClicShoppingAdmin namespace.
 * This service ensures the appropriate setup and cleanup of resources in the application.
 */
class CfgModulesAdmin implements \ClicShopping\OM\ServiceInterface
{
  public static function start(): bool
  {
    Registry::set('CfgModulesAdmin', new CfgModulesAdminClass());

    return true;
  }

  public static function stop(): bool
  {
    return true;
  }
}
