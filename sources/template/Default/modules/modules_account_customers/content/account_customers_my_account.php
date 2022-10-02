<?php
  /**
   *
   *  @copyright 2008 - https://www.clicshopping.org
   *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
   *  @Licence GPL 2 & MIT
   *  @Info : https://www.clicshopping.org/forum/trademark/
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
        <div class="col moduleAccountCustomersMyAccountTitle"><h3><?php echo CLICSHOPPING::getDef('module_account_customers_my_account_customer'); ?></h3></div>
        <div class="col text-end">
          <i class="bi bi-person-fill moduleAccountCustomersAccountIcon"></i>
        </div>
      </div>
    </div>
    <div class="separator"></div>
    <div class="card-block">
      <div class="separator"></div>
      <div class="card-text">
        <div class="separator"></div>
        <div class="moduleAccountCustomersMyAccountListAccount">
          <div class="col-md-12" id="accountCustomersMyAccountAddress">
            <div><i class="bi bi-arrow-right-square-fill moduleAccountCustomersAccountIconArrow"></i> <?php echo HTML::link(CLICSHOPPING::link(null, 'Account&Edit'), CLICSHOPPING::getDef('module_account_customers_my_account_information')); ?></div>
            <div><i class="bi bi-arrow-right-square-fill moduleAccountCustomersAccountIconArrow"></i> <?php echo HTML::link(CLICSHOPPING::link(null, 'Account&AddressBook'), CLICSHOPPING::getDef('module_account_customers_my_account_address_book')); ?></div>
            <div class="separator"></div>
            <div class="hr"></div>
            <div class="separator"></div>
            <?php
              if (\defined('MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_TITLE_STATUS')) {
                if (MODULE_ACCOUNT_CUSTOMERS_MY_FEEDBACK_TITLE_STATUS == 'True') {
            ?>
              <div><i class="bi bi-arrow-right-square-fill moduleAccountCustomersAccountIconArrow"></i>< ?php echo HTML::link(CLICSHOPPING::link(null, 'Account&MyFeedBack'), CLICSHOPPING::getDef('module_account_customers_my_account_my_feed_back')); ?></div>
              <div class="hr"></div>
            <?php
                }
              }
            ?>
            <div class="separator"></div>
            <?php
              if (\defined('MODULE_ACCOUNT_CUSTOMERS_PASSWORD_TITLE_STATUS') && MODULE_ACCOUNT_CUSTOMERS_PASSWORD_TITLE_STATUS == 'True') {
            ?>
              <div class="separator"></div>
              <div><i class="bi bi-arrow-right-square-fill moduleAccountCustomersAccountIconArrow"></i> <?php echo HTML::link(CLICSHOPPING::link(null, 'Account&Password'), CLICSHOPPING::getDef('module_account_customers_my_account_password')); ?></div>
            <?php
              }
            ?>
            <div><i class="bi bi-arrow-right-square-fill moduleAccountCustomersAccountIconArrow"></i> <?php echo HTML::link(CLICSHOPPING::link(null, 'Account&Gdpr'), CLICSHOPPING::getDef('module_account_customers_my_account_gdpr')); ?></div>
            <div class="separator"></div>
            <?php
              if (\defined('CLICSHOPPING_APP_RETURN_ORDERS_RO_STATUS') && CLICSHOPPING_APP_RETURN_ORDERS_RO_STATUS == 'True') {
            ?>
              <div class="separator"></div>
              <div><i class="bi bi-arrow-right-square-fill moduleAccountCustomersAccountIconArrow"></i> <?php echo HTML::link(CLICSHOPPING::link(null, 'Account&ProductReturnHistory'), CLICSHOPPING::getDef('module_account_customers_my_account_return_history')); ?></div>
            <?php
              }
            ?>
             <div class="separator"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>