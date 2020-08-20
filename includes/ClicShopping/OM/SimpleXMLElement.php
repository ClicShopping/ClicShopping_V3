<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\OM;

  class SimpleXMLElement extends \SimpleXMLElement
  {
    public function addChildCData(string $name, string $value)
    {
      $child = $this->addChild($name);
      $this->addCData($value);
    }

    protected function addCData(string $value)
    {
      $node = dom_import_simplexml($this);
      if ($node !== false) {
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($value));
      }
    }
  }
