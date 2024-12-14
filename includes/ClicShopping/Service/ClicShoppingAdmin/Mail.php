<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Service\ClicShoppingAdmin;

use ClicShopping\OM\Registry;

use ClicShopping\OM\Mail as MailClass;
/**
 * The Mail service class for ClicShoppingAdmin initializes and manages the Mail service.
 * It interacts with the Registry to set up the Mail instance.
 *
 * @package ClicShopping\Service\ClicShoppingAdmin
 */
class Mail implements \ClicShopping\OM\ServiceInterface
{
  public static function start(): bool
  {
    Registry::set('Mail', new MailClass());

    return true;
  }

  public static function stop(): bool
  {
    return true;
  }
}
