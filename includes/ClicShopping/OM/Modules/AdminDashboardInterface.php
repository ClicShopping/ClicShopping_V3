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
 * Interface AdminDashboardInterface
 *
 * Defines the structure for an admin dashboard module, including methods
 * for rendering output, installation, configuration, and operational checks.
 */
interface AdminDashboardInterface
{
  public function getOutput();

  public function install();

  public function keys();

  public function isEnabled();

  public function check();

  public function remove();
}
