<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

?>
<div class="separator"></div>
<div class="col-md-6 float-md-left">
  <div class="card">
    <div class="card-header">
      <?php echo $CLICSHOPPING_PayPal->getDef('payflow_live_title') . ' ' . $CLICSHOPPING_PayPal->getDef('payflow'); ?>
    </div>
    <div class="card-block">

      <div class="row">
        <div class="col-md-12">
          <div class="form-group row">
            <label for="live_partner"
                   class="col-5 col-form-label"><?php echo $CLICSHOPPING_PayPal->getDef('payflow_partner'); ?></label>
            <div class="col-md-12">
              <?php echo HTML::inputField('live_partner', CLICSHOPPING_APP_PAYPAL_PF_LIVE_PARTNER, 'id="live_partner"'); ?>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group row">
            <label for="live_vendor"
                   class="col-5 col-form-label"><?php echo $CLICSHOPPING_PayPal->getDef('payflow_merchant_login'); ?></label>
            <div class="col-md-12">
              <?php echo HTML::inputField('live_vendor', CLICSHOPPING_APP_PAYPAL_PF_LIVE_VENDOR, 'id="live_vendor"'); ?>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group row">
            <label for="live_user"
                   class="col-5 col-form-label"><?php echo $CLICSHOPPING_PayPal->getDef('payflow_user'); ?></label>
            <div class="col-md-12">
              <?php echo HTML::inputField('live_user', CLICSHOPPING_APP_PAYPAL_PF_LIVE_USER, 'id="live_user"'); ?>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group row">
            <label for="live_password"
                   class="col-5 col-form-label"><?php echo $CLICSHOPPING_PayPal->getDef('payflow_password'); ?></label>
            <div class="col-md-12">
              <?php echo HTML::inputField('live_password', CLICSHOPPING_APP_PAYPAL_PF_LIVE_PASSWORD, 'id="live_password"'); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="col-md-6 float-md-right">
  <div class="card">
    <div class="card-header">
      <?php echo $CLICSHOPPING_PayPal->getDef('payflow_sandbox_title'); ?>
    </div>
    <div class="card-block">

      <div class="row">
        <div class="col-md-12">
          <div class="form-group row">
            <label for="sandbox_partner"
                   class="col-5 col-form-label"><?php echo $CLICSHOPPING_PayPal->getDef('payflow_partner'); ?></label>
            <div class="col-md-12">
              <?php echo HTML::inputField('sandbox_partner', CLICSHOPPING_APP_PAYPAL_PF_SANDBOX_PARTNER, 'id="sandbox_partner"'); ?>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group row">
            <label for="sandbox_vendor"
                   class="col-5 col-form-label"><?php echo $CLICSHOPPING_PayPal->getDef('payflow_merchant_login'); ?></label>
            <div class="col-md-12">
              <?php echo HTML::inputField('sandbox_vendor', CLICSHOPPING_APP_PAYPAL_PF_SANDBOX_VENDOR, 'id="sandbox_vendor"'); ?>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group row">
            <label for="sandbox_user"
                   class="col-5 col-form-label"><?php echo $CLICSHOPPING_PayPal->getDef('payflow_user'); ?></label>
            <div class="col-md-12">
              <?php echo HTML::inputField('sandbox_user', CLICSHOPPING_APP_PAYPAL_PF_SANDBOX_USER, 'id="sandbox_user"'); ?>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group row">
            <label for="sandbox_password"
                   class="col-5 col-form-label"><?php echo $CLICSHOPPING_PayPal->getDef('payflow_password'); ?></label>
            <div class="col-md-12">
              <?php echo HTML::inputField('sandbox_password', CLICSHOPPING_APP_PAYPAL_PF_SANDBOX_PASSWORD, 'id="sandbox_password"'); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>