<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\HTML;
  use ClicShopping\OM\DateTime;
  use ClicShopping\OM\CLICSHOPPING;
?>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
  <div class="page-title moduleAccountCustomersHistoryInfoHeadingHorderHistory"><h3><?php echo CLICSHOPPING::getDef('module_account_customers_history_info_heading_order_history'); ?></h3></div>
  <div class="separator"></div>
  <div class="d-flex flex-wrap">
<?php
  while ($Qstatuse->fetch()) {
    echo '<div class="col-md-12">';
    echo '<span class="text-muted"><i class="bi bi-clock-fill"></i> ' . DateTime::toShort($Qstatuse->value('date_added')) . '</span>';
    echo '<span style="padding-left:20px;">' . $Qstatuse->value('orders_status_name') . '</span>';
    echo '<div>';
    echo '<p>' . (empty($Qstatuse->value('comments')) ? '&nbsp;' : '<blockquote>' . nl2br(HTML::outputProtected($Qstatuse->value('comments'))) . '</blockquote>') . '</p>';
    echo '</div>';
    echo '</div>';
  }
?>
  </div>
  <div class="separator"></div>
  <div class="hr"></div>
</div>
