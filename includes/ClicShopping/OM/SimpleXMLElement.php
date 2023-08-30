<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM;

class SimpleXMLElement extends \SimpleXMLElement
{
  /**
   * @param string $name
   * @param string $value
   */
  public function addChildCData(string $name, string $value)
  {
//      $child = $this->addChild($name);
    static::addCData($value);
  }

  /**
   * @param string $value
   */
  protected function addCData(string $value)
  {
    $node = dom_import_simplexml($this);

    if ($node !== false) {
      $no = $node->ownerDocument;
      $node->appendChild($no->createCDATASection($value));
    }
  }
}
