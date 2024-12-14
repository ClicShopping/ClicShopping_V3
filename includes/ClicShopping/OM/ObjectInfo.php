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
 * Class ObjectInfo
 *
 * ObjectInfo is a utility class that dynamically assigns properties to the object
 * based on the provided associative array. The class facilitates the dynamic population
 * of object properties at runtime.
 */
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