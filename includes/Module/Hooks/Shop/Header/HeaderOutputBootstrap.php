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
use function defined;

class HeaderOutputBootstrap
{
  /**
   * Generates and returns the HTML output for loading Bootstrap and custom template CSS stylesheets.
   * If the related meta tag for enabling theme selection is not installed or is disabled, this method
   * will return the necessary HTML for injecting the required stylesheets into a webpage.
   *
   * @return string|false The generated HTML string for including Bootstrap and custom template CSS,
   *                      or false if the theme selection meta tag is enabled.
   */
  public function display()
  {
    $CLICSHOPPING_Template = Registry::get('Template');

//Note : Could be relation with a meta tag allowing to implement a new boostrap theme : Must be installed
    if (!defined('MODULE_HEADER_TAGS_BOOTSTRAP_SELECT_THEME') || MODULE_HEADER_TAGS_BOOTSTRAP_SELECT_THEME == 'False') {
      $output = '<!-- CSS only -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">' . "\n";
      $output .= '<link rel="stylesheet" media="screen, print" href="' . $CLICSHOPPING_Template->getTemplateCSS() . '" />' . "\n";
      $output .= '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">';

      return $output;
    } else {
      return false;
    }
  }
}