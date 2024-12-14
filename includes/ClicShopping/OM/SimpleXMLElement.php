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
 * Adds a child element with a CDATA section as its value.
 *
 * @param string $name The name of the child element to add.
 * @param string $value The value to be wrapped in a CDATA section.
 */
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
