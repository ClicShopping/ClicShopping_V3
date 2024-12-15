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

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Marketing\Specials\Classes\Shop\SpecialsClass;
/**
 * The Specials class provides methods to manage the lifecycle of the Specials service in the ClicShopping framework.
 * This service initializes the SpecialsClass functionality and ensures that scheduled and expired specials are processed.
 *
 * Implements the ServiceInterface to adhere to the structure of ClicShopping service components.
 */
class Specials implements \ClicShopping\OM\ServiceInterface
{
  /**
   * Initializes the SpecialsClass functionality if the required file exists.
   *
   * @return bool Returns true if the SpecialsClass is successfully initialized, otherwise false.
   */
  public static function start(): bool
  {
    if (is_file(CLICSHOPPING::BASE_DIR . 'Apps/Marketing/Specials/Classes/Shop/SpecialsClass.php')) {
      Registry::set('SpecialsClass', new SpecialsClass());

      $CLICSHOPPING_Specials = Registry::get('SpecialsClass');

      $CLICSHOPPING_Specials->scheduledSpecials();
      $CLICSHOPPING_Specials->expireSpecials();

      return true;
    } else {
      return false;
    }
  }

  /**
   * Stops the execution or performs the necessary termination operations.
   *
   * @return bool Returns true to indicate that the stop operation was successful.
   */
  public static function stop(): bool
  {
    return true;
  }
}
