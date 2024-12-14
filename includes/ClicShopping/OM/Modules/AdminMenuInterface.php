<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


namespace ClicShopping\OM\Modules;

/**
 * Interface AdminMenuInterface
 *
 * Defines the contract for an admin menu module within the ClicShopping application.
 * Classes implementing this interface must provide functionality to execute specific operations
 * related to the administration menu.
 */

interface AdminMenuInterface
{
  public static function execute();
}
