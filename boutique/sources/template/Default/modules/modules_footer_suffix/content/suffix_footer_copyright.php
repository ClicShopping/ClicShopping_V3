<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */

use ClicShopping\OM\CLICSHOPPING;
?>
<div class="separator"></div>
<div class="hr"></div>
<div class="text-md-center footerSuffix">
  <div class="footerSuffixCopyright">
    <span class="footerSuffixCopyright"><?php echo CLICSHOPPING::getDef('modules_footer_suffix_copyright_text') . ' ' . $date_copyright; ?></span>
  </div>
  <div class="footerSuffixTrademark">
    <span class="footerSuffixTrademark"><?php echo  $logo .  CLICSHOPPING::getDef('modules_footer_suffix_trademark_text'); ?></span>
  </div>
</div>