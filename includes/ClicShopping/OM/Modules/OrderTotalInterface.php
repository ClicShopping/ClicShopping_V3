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
 * Interface OrderTotalInterface
 *
 * Represents the contract for managing and processing order total modules.
 */
interface OrderTotalInterface
{
  public function process();

  public function check();

  public function install();

  public function remove();

  public function keys();
}
