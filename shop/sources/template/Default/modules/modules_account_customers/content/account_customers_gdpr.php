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

use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

  echo $form;
?>
<div class="col-md-<?php echo $content_width; ?>">

  <div class="page-header AccountCustomersGdrp"><h3><?php echo CLICSHOPPING::getDef('module_account_customers_gdpr_title'); ?></h3></div>

  <div class="separator"></div>
  <div><?php echo CLICSHOPPING::getDef('module_account_customers_gdpr_account_intro'); ?></div>
  <div class="separator"></div>
  <div class="form-group">
    <blockquote>
      <div class="separator"></div>
      <div id="gdpr">
<?php
  if (is_array($files_get)) {
    foreach ($files_get as $value) {
      if (!empty($value['name'])) {
       echo $CLICSHOPPING_Hooks->output('Account', $value['name'], null, 'display');
      }
    }
  }
?>
      </div>
      <div>
        <?php echo CLICSHOPPING::getDef('module_account_customers_more_info') . ' ' . HTML::link(CLICSHOPPING::link(null, 'Info&Contact'), CLICSHOPPING::getDef('module_account_customers_contact_us')); ?>
      </div>
    </blockquote>
  </div>

  <div class="col-md-12">
    <div class="control-group">
      <div class="controls">
        <div class="buttonSet">
          <span class="col-md-2"><?php echo HTML::button(CLICSHOPPING::getDef('button_back'), null, CLICSHOPPING::link(null, 'Account&Main'), 'primary');  ?></span>
          <span class="col-md-2 float-md-right text-md-right"><?php echo HTML::button(CLICSHOPPING::getDef('button_continue'), null, null, 'success');  ?></span>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
  echo $endform;