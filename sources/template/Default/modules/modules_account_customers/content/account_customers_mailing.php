<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;

?>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>

  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="col-md-11 moduleAccountCustomersMyAccountTitle">
          <h3><?php echo CLICSHOPPING::getDef('module_account_customers_email_mailing_title'); ?></h3></div>
        <div class="col-md-1 text-end">
          <i class="bi bi-envelope-fill moduleAccountCustomersNotificationsIcon"></i>
        </div>
      </div>
    </div>

    <div class="card-block">
      <div class="separator"></div>
      <div class="card-text">
        <div class="moduleAccountCustomersNotificationsList">
          <div class="col-md-12">
            <div><i
                class="bi bi-arrow-right-square-fill moduleAccountCustomersNotificationsIconArrow"></i><?php echo HTML::link(CLICSHOPPING::link(null, 'Account&Newsletters'), CLICSHOPPING::getDef('module_account_customers_email_mailing_newsletters')); ?>
            </div>
            <div><i
                class="bi bi-arrow-right-square-fill moduleAccountCustomersNotificationsIconArrow"></i><?php echo HTML::link(CLICSHOPPING::link(null, 'Account&Notifications'), CLICSHOPPING::getDef('module_account_customers_email_mailing_products')); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>