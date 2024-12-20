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
 * Interface IsInterface
 *
 * This interface defines a method for executing a specific functionality.
 * Implementing classes are required to provide the logic for the `execute` method.
 */
interface IsInterface
{
  /**
   * Executes a specific operation based on the provided value.
   *
   * @param mixed $value The input value required for execution.
   * @return bool Returns true if the operation was successful, otherwise false.
   */
  public static function execute($value): bool;
}
