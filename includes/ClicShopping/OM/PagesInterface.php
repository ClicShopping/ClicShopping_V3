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
 * Interface PagesInterface
 *
 * Provides an interface to manage page files within the ClicShopping framework.
 */
interface PagesInterface
{
  public function getFile();

  public function setFile($file);
}
