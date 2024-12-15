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
   * Constructor method to initialize the object with provided data.
   *
   * @param array $object_array An associative array containing object information.
   * @return void
   */
  public function __construct(array $object_array)
  {
    $this->objectInfo($object_array);
  }

  /**
   * Populates the properties of the object with the key-value pairs from the provided array.
   *
   * @param array $object_array An associative array where keys correspond to property names and values are the values to be assigned.
   * @return void
   */
  public function objectInfo(array $object_array)
  {
    foreach ($object_array as $key => $value) {
      $this->$key = $value;
    }
  }
}