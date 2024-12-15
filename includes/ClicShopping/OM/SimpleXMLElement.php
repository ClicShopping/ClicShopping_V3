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
   * Adds a CDATA section with a specified value to a child node of the current XML element.
   *
   * @param string $name The name of the child node to which the CDATA section will be added.
   * @param string $value The value to be enclosed in the CDATA section.
   * @return void
   */
  public function addChildCData(string $name, string $value)
  {
//      $child = $this->addChild($name);
    static::addCData($value);
  }

  /**
   * Adds a CDATA section with the provided value to the current XML node.
   *
   * @param string $value The value to be wrapped in a CDATA section and added to the XML node.
   * @return void
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
