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
<div class="col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="col-md-10 mdouleAccountCustomersHistoryInfoInvoicePdfText"><h3><?php echo CLICSHOPPING::getDef('module_account_customers_history_info_invoice_pdf_text'); ?></h3></div>
        <div class="col-md-2 text-md-right"><?php echo $print_invoice_pdf; ?></div>
      </div>
    </div>
  </div>
</div>
