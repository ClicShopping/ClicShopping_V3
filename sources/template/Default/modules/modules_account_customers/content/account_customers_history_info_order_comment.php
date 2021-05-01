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

use ClicShopping\OM\DateTime;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Sites\Shop\Pages\Account\Classes\HistoryInfo;
?>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
  <div class="card">
    <div class="card-header">
      <div class="modulesAccountCustomerHistoryInforOrderCommentHeadingHistory"><h3><?php echo CLICSHOPPING::getDef('module_account_customers_history_info_order_comment_heading_history'); ?></h3></div>
    </div>
    <div class="card-block">
      <div class="separator"></div>
      <div class="card-text">
        <div class="row">
<?php

  foreach ($Qstatuse as $value) {
    $customer_support = HistoryInfo::getHistoryInfoSupportCustomer($value['orders_status_support_id']);

    echo '<div class="separator"></div>';
    echo '<div class="col-md-12">';
    echo '<span class="col-md-4 text-muted"><i class="bi bi-arrow-right-square-fill" aria-hidden="true">&nbsp;&nbsp;&nbsp;</i>' . DateTime::toShort($value['date_added']) . '</span> ';
    echo '<span class="col-md-8 modulesAccountCustomerHistoryInforOrderCommentStatusName">' . $value['orders_status_name'] . '</span>';

    if (!empty($customer_support['orders_status_support_name'])) {
      echo '<div class="col-md-12 modulesAccountCustomerHistoryInforOrderCommentStatusSupport">' . CLICSHOPPING::getDef('module_account_customers_history_info_order_comment_support')  . $customer_support['orders_status_support_name'] . '</div>';
    }

    echo '</div>';
    echo '<div class="separator"></div>';
    echo '<div class="col-md-12">';
    echo '<span class="col-md-4"></span>';
    echo '<span class="col-md-8 modulesAccountCustomerHistoryInforOrderCommentText">' . (empty($value['comments']) ? '&nbsp;' : '' . nl2br(HTML::outputProtected($value['comments'])) . '</span>') . '</span>';
    echo '</div>';
    echo '<div class="separator"></div>';
  }
?>
        </div>
      </div>
    </div>
  </div>
</div>
