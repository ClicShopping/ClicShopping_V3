<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\CLICSHOPPING;
?>
<div class="col-md-<?php echo $content_width; ?> modulesHeaderNoscript">
  <noscript>
    <div class="alert alert-warning" role="alert">
      <div class="modulesHeaderNoscriptInner text-center">
        <?php echo CLICSHOPPING::getDef('module_header_noscript_text'); ?>
      </div>
    </div>
  </noscript>
</div>
