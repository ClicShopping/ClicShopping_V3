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
  /**
   * Retrieves a file.
   *
   * This method is used to obtain a file based on internal logic or criteria.
   *
   * @return mixed The file or its representation, depending on the implementation.
   */
  public function getFile();

  /**
   *
   * @param mixed $file The file to be set.
   */
  public function setFile($file);
}
