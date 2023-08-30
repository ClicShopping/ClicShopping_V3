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
use ClicShopping\OM\DateTime;
use ClicShopping\OM\HTML;

?>
<div class="col-md-<?php echo $content_width; ?>" id="productReturnHistoryInfo">
  <div class="separator"></div>
  <div class="row">
    <div class="col-md-12">
      <div class="page-title Account_CustomersReturn">
        <h3><?php echo CLICSHOPPING::getDef('module_account_product_return_history_info_heading_title_info'); ?></h3>
      </div>
    </div>
  </div>
  <?php
  /*
  * Summary
  */
  ?>
  <div class="separator"></div>
  <div class="separator"></div>
  <div class="separator"></div>
  <div class="row" id="productReturnSummary">
    <div class="col-md-12"><h5><?php echo CLICSHOPPING::getDef('module_account_product_return_history_summary') ?></h5>
    </div>
    <div class="separator"></div>
    <div class="separator"></div>
    <div class="col-md-12">
      <div><?php echo '<strong> ' . CLICSHOPPING::getDef('module_account_product_return_history_return_ref') . '</strong> ' . $return_ref; ?></div>
      <div class="row">
        <span class="col-md-6">
          <?php echo '<strong> ' . CLICSHOPPING::getDef('module_account_product_return_history_product_name') . '</strong> ' . $product_name; ?>
        </span>
        <span class="col-md-6 text-end">
          <?php echo '<strong> ' . CLICSHOPPING::getDef('module_account_product_return_history_product_model') . '</strong> ' . $product_model; ?>
        </span>
      </div>
      <div><?php echo '<strong> ' . CLICSHOPPING::getDef('module_account_product_return_history_return_date_added') . '</strong> ' . DateTime::toShort($date_added); ?></div>
      <div><?php echo '<strong> ' . CLICSHOPPING::getDef('module_account_product_return_history_return_qty') . '</strong> ' . $return_qty; ?></div>
      <div><?php echo '<strong> ' . CLICSHOPPING::getDef('module_account_product_return_history_return_oder_id') . '</strong> ' . $oID; ?></div>
    </div>
  </div>

  <div class="separator"></div>
  <div class="hr"></div>
  <div class="separator"></div>
  <?php
  /*
   * Status
  */

  foreach ($HistoryCheckInfo as $value) {
    ?>
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
          <span class="col-md-6 text-start">
            <?php echo CLICSHOPPING::getDef('module_account_product_return_history_status'); ?>
          </span>
          </div>
        </div>

        <div class="card-body">
          <div class="card-text">
            <div class="col-md-12">
              <div class="form-group row">
                <div class="col-md-6">
                  <?php echo '<strong>' . CLICSHOPPING::getDef('module_account_product_return_history_status_name') . '</strong> ' . $value['name']; ?>
                </div>
                <div class="col-md-6 text-end">
                  <?php echo '<strong>' . CLICSHOPPING::getDef('module_account_product_return_history_date_added') . '</strong> ' . DateTime::toShort($value['date_added']); ?>
                </div>
              </div>
              <div class="separator"></div>
              <div class="col-md-12">
                <strong><?php echo CLICSHOPPING::getDef('module_account_product_return_history_info_comment'); ?></strong>
              </div>
              <div class="col-md-12"><?php echo $value['comment']; ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="separator"></div>
    <?php
  }
  /*
  * comment
  */
  ?>
  <div class="separator"></div>
  <hr>
  <div class="separator"></div>
  <?php echo $form; ?>
  <div class="row" id="productReturnHistoryInfoComment">
    <div class="col-md-12">
      <h5><?php echo CLICSHOPPING::getDef('module_account_product_return_history_write_comment') ?></h5></div>
    <div class="separator"></div>
    <div class="col-md-12">
      <div class="row">
        <?php echo HTML::textAreaField('comment', null, 250, 5, 'required  aria-required="true" id="comment" aria-describedby="' . CLICSHOPPING::getDef('module_account_product_return_faulty') . '" placeholder="' . CLICSHOPPING::getDef('module_account_product_return_faulty') . '"'); ?>
      </div>
    </div>
    <div class="separator"></div>
    <div class="separator"></div>
    <div class="col-md-12">
      <div class="row">
        <span
          class="col-md-6"><?php echo HTML::button(CLICSHOPPING::getDef('module_account_product_return_history_button_back'), null, CLICSHOPPING::link(null, 'Account&ProductReturnHistory'), 'primary'); ?></span>
        <span
          class="col-md-6 text-end"><?php echo HTML::button(CLICSHOPPING::getDef('module_account_product_return_history_button_send'), null, null, 'success'); ?></span>
      </div>
    </div>
  </div>
  <?php echo $endform; ?>
</div>