<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Module\Hooks\Shop\Header;

use ClicShopping\OM\Registry;

class HeaderOutputAMetaHeader
{
  /**
   * Generates and returns the output string containing app header tags and header tag blocks.
   *
   * @return string Concatenated string consisting of app header tags and header tag blocks.
   */
  public function display(): string
  {
    $CLICSHOPPING_Template = Registry::get('Template');

    $output = $CLICSHOPPING_Template->getAppsHeaderTags() . "\n";
    $output .= $CLICSHOPPING_Template->getBlocks('header_tags') . "\n";

    return $output;
  }
}