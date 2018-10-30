<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4 
 *
 *
 */

  namespace ClicShopping\OM;

  class ObjectInfo {

    public function __construct(array $object_array) {
      $this->objectInfo($object_array);
    }

    public function objectInfo(array $object_array) {
      foreach ($object_array as $key => $value) {
        $this->$key = $value;
      }
    }
  }