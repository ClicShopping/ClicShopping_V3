<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM;

/**
 * Interface PagesActionsInterface
 *
 * Defines the structure for pages action classes within the ClicShopping framework.
 * Classes implementing this interface must provide methods for executing actions
 * and determining if the action is executed via Remote Procedure Call (RPC).
 */
interface PagesActionsInterface
{
  public function execute();

  public function isRPC();
}
