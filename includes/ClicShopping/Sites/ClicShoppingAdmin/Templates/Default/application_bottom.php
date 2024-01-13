<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\Registry;

$CLICSHOPPING_LoggerAdmin = Registry::get('LoggerAdmin');

if (DISPLAY_PAGE_PARSE_TIME == 'true') {
  if (!\is_object($CLICSHOPPING_LoggerAdmin)) {
    $CLICSHOPPING_LoggerAdmin = Registry::get('LoggerAdmin');
  }

  echo '<div class="row">';
  echo '<div class="col-md-12 alert alert-info">';
  echo $CLICSHOPPING_LoggerAdmin->timerStop(DISPLAY_PAGE_PARSE_TIME);
  echo '</div>';
  echo '</div>';
  echo '<div class="mt-1"></div>';
}
