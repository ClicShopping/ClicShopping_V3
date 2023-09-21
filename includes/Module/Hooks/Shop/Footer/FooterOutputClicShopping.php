<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Module\Hooks\Shop\Footer;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\Registry;

class FooterOutputClicShopping
{
  /**
   * @return string
   */
  public function display(): string
  {
    $CLICSHOPPING_Template = Registry::get('Template');

    $output = '<!--ClicShopping Script start-->' . "\n";
    $output .= '<script defer src="' . CLICSHOPPING::link($CLICSHOPPING_Template->getTemplateDefaultJavaScript('clicshopping/footer.js')) . '"></script>' . "\n";
    $output .= '<!--End ClicShopping Script-->' . "\n";

    return $output;
  }
}