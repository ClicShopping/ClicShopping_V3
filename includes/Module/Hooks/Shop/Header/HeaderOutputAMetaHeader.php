<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Module\Hooks\Shop\Header;

use ClicShopping\OM\Registry;

class HeaderOutputAMetaHeader
{
  /**
   * @return string
   */
  public function display(): string
  {
    $CLICSHOPPING_Template = Registry::get('Template');

    $output = $CLICSHOPPING_Template->getAppsHeaderTags() . "\n";
    $output .= $CLICSHOPPING_Template->getBlocks('header_tags') . "\n";

    return $output;
  }
}