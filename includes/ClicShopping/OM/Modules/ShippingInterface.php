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
 * ShippingInterface provides the blueprint for implementing shipping-related functionalities.
 *
 * It defines methods for obtaining shipping quotes, performing validation checks,
 * managing installation or removal of shipping modules, and retrieving configurable parameters.
 */
interface ShippingInterface
{
  public function quote();

  public function check();

  public function install();

  public function remove();

  public function keys();
}
