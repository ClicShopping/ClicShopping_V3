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
<noscript>
  <div class="col-md-<?php echo $content_width; ?> modulesHeaderNoscript">
    <noscript>
      <div class="alert alert-warning">
        <div class="modulesHeaderNoscriptInner text-md-center">
          <?php echo CLICSHOPPING::getDef('module_header_noscript_text'); ?>
        </div>
      </div>
    </noscript>
  </div>
</noscript>