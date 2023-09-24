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

class ObjectInfo
{
  /**
   * ObjectInfo constructor.
   * @param array $object_array
   */
  public function __construct(array $object_array)
  {
    $this->objectInfo($object_array);
  }

  /**
   * @param array $object_array
   */
  public function objectInfo(array $object_array)
  {
    foreach ($object_array as $key => $value) {
      $this->$key = $value;
    }
  }
}