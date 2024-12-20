<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Service\Shop;

use ClicShopping\OM\Registry;
/**
 * Service for managing currencies geolocation in the shop.
 *
 * This service integrates with the shop functionality to handle
 * currency settings based on geolocation. It provides methods
 * to start and stop the service. The service utilizes the Hooks
 * system for extending functionality.
 */
class CurrenciesGeolocalisation implements \ClicShopping\OM\ServiceInterface
{
  /**
   * Starts the execution of the currency geolocation process across all shops by triggering the appropriate hook.
   *
   * @return bool Returns true upon successful initiation of the process.
   */
  public static function start(): bool
  {
// hook has impact in all shop
    Registry::get('Hooks')->call('AllShop', 'CurrenciesGeolocalisation');

    return true;
  }

  /**
   * Stops the current process or execution.
   *
   * @return bool Returns true when the stop process is successfully completed.
   */
  public static function stop(): bool
  {
    return true;
  }
}
