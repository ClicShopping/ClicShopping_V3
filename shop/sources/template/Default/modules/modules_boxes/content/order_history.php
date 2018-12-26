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
<section class="boxe_order_history" id="boxe_order_history">
  <div class="card boxeContainerHistory">
    <div class="card-img-top boxeBannerContentsHistory"><?php echo $order_history_banner; ?></div>
    <div class="card-header boxeHeadingHistory">
      <span class="card-title boxeTitleHistory"><?php echo CLICSHOPPING::getDef('module_boxes_order_history_box_title'); ?></span>
    </div>
    <div class="card-block boxeContentArroundHistory">
      <div class="card-text boxeContentsHistory"><?php echo $customer_orders_string; ?></div>
    </div>
  </div>
</section>