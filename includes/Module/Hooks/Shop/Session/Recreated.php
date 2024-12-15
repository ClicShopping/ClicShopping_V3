<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


namespace ClicShopping\OM\Module\Hooks\Shop\Session;

use ClicShopping\OM\Hash;

class Recreated
{
  /**
   * Resets the session token with a newly generated value.
   *
   * @param mixed $parameters Additional parameters for the execution. Not used in the current implementation.
   * @return void
   */
  public function execute($parameters)
  {
// reset session token
    $_SESSION['sessiontoken'] = md5(Hash::getRandomInt() . Hash::getRandomInt() . Hash::getRandomInt() . Hash::getRandomInt());
  }
}
