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
 * Interface ServiceInterface
 *
 * Defines the structure for a service within the application by requiring
 * the implementation of methods to start and stop the service.
 */
interface ServiceInterface
{
  /**
   * Starts the execution of the defined process or service.
   */
  public static function start();

  /**
   * Stops the execution of the defined process or service.
   */
  public static function stop();
}