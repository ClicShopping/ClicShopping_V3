<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;

?>
<div class="separator"></div>
<div class="hr"></div>
<div class="text-center footerSuffix">
  <div class="footerSuffixCopyright">
    <span
      class="footerSuffixCopyright"><?php echo CLICSHOPPING::getDef('modules_footer_suffix_copyright_text') . ' ' . $shop_owner_copyright; ?></span>
  </div>
  <div class="footerSuffixTrademark">
    <span
      class="footerSuffixTrademark"><?php echo $logo . ' ' . CLICSHOPPING::getDef('modules_footer_suffix_trademark_text') . ' ' . $clicshopping_copyright; ?></span>
  </div>
</div>