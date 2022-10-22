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
  use ClicShopping\OM\CLICSHOPPING;
?>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
  <div class="row">
    <div class="col-md-12">
      <div class="page-title AccountCustomersReturn"><h3><?php echo CLICSHOPPING::getDef('module_account_product_return_history_heading_title'); ?></h3></div>
    </div>
  </div>
  <div class="separator"></div>
  <div class="hr"></div>
  <div class="separator"></div>
  <?php
    foreach ($historyCheck as $value) {
  ?>
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <span class="col-md-6 text-start">
            <?php echo CLICSHOPPING::getDef('module_account_product_return_history_ref') . '  ' . $value['return_ref']; ?>
          </span>
          <span class="col-md-6 text-end">
            <?php
              if ($value['opened'] == 0) {
                echo HTML::button(CLICSHOPPING::getDef('module_account_product_return_history_info'), null, CLICSHOPPING::link(null, 'Account&ProductReturnHistoryInfo&rId=' . $value['return_id']), 'info', null, 'sm');
              } else {
                echo HTML::button(CLICSHOPPING::getDef('module_account_product_return_closed'), null, null, 'danger', null, 'sm');

              }
            ?>
          </span>
        </div>
      </div>

      <div class="card-body">
        <div class="card-text">
          <div class="col-md-12">
            <div class="form-group row">
              <div class="col-md-6">
                <?php echo CLICSHOPPING::getDef('module_account_product_return_history_product_name') . ' ' . $value['product_name']; ?>
              </div>
              <div class="col-md-6 text-end">
                <?php echo CLICSHOPPING::getDef('module_account_product_return_history_product_product_model') . ' ' . $value['product_model']; ?>
              </div>
            </div>
            <div><?php echo CLICSHOPPING::getDef('module_account_product_return_history_product_order') . ' ' . $value['order_id']; ?></div>
            <div><?php echo CLICSHOPPING::getDef('module_account_product_return_history_product_quantity') . ' ' . $value['quantity']; ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <?php
    }
  ?>
  <div class="separator"></div>
  <div class="col-md-12">
    <div class="row">
      <div class="col-md-12"><?php echo HTML::button(CLICSHOPPING::getDef('button_back'), CLICSHOPPING::link(null, 'Account&Main'), null, 'primary'); ?></div>
    </div>
  </div>
</div>