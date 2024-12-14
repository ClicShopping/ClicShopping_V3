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
 * Interface HeaderTagsInterface
 *
 * Defines a contract for managing header tags in a system.
 * Provides methods for output generation, installation, configuration keys retrieval,
 * status checking, and removal functionalities.
 */
interface HeaderTagsInterface
{
  public function getOutput();

  public function install();

  public function keys();

  public function isEnabled();

  public function check();

  public function remove();
}
