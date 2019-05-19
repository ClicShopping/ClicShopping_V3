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
      <?php echo $CLICSHOPPING_PayPal->getDef('paypal_live_title') . ' ' . $CLICSHOPPING_PayPal->getDef('paypal'); ?>
    </div>
    <div class="card-block">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group row">
            <label for="live_username"
                   class="col-5 col-form-label"><?php echo $CLICSHOPPING_PayPal->getDef('paypal_api_username'); ?></label>
            <div class="col-md-12">
              <?php echo HTML::inputField('live_username', CLICSHOPPING_APP_PAYPAL_LIVE_API_USERNAME, 'id="live_username"'); ?>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group row">
            <label for="live_password"
                   class="col-5 col-form-label"><?php echo $CLICSHOPPING_PayPal->getDef('paypal_api_password'); ?></label>
            <div class="col-md-12">
              <?php echo HTML::inputField('live_password', CLICSHOPPING_APP_PAYPAL_LIVE_API_PASSWORD, 'id="live_password"'); ?>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group row">
            <label for="live_signature"
                   class="col-5 col-form-label"><?php echo $CLICSHOPPING_PayPal->getDef('paypal_api_signature'); ?></label>
            <div class="col-md-12">
              <?php echo HTML::inputField('live_signature', CLICSHOPPING_APP_PAYPAL_LIVE_API_SIGNATURE, 'id="live_signature"'); ?>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group row">
            <label for="live_merchant_id"
                   class="col-5 col-form-label"><?php echo $CLICSHOPPING_PayPal->getDef('paypal_merchant_id'); ?></label>
            <div class="col-md-12">
              <?php echo HTML::inputField('live_merchant_id', CLICSHOPPING_APP_PAYPAL_LIVE_MERCHANT_ID, 'id="live_merchant_id"'); ?>
              <span class="form-text"><?php echo $CLICSHOPPING_PayPal->getDef('paypal_merchant_id_desc'); ?></span>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group row">
            <label for="live_email"
                   class="col-5 col-form-label"><?php echo $CLICSHOPPING_PayPal->getDef('paypal_email_address'); ?></label>
            <div class="col-md-12">
              <?php echo HTML::inputField('live_email', CLICSHOPPING_APP_PAYPAL_LIVE_SELLER_EMAIL, 'id="live_email"'); ?>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group row">
            <label for="live_email_primary"
                   class="col-5 col-form-label"><?php echo $CLICSHOPPING_PayPal->getDef('paypal_primary_email_address'); ?></label>
            <div class="col-md-12">
              <?php echo HTML::inputField('live_email_primary', CLICSHOPPING_APP_PAYPAL_LIVE_SELLER_EMAIL_PRIMARY, 'id="live_email_primary"'); ?>
              <span
                class="form-text"><?php echo $CLICSHOPPING_PayPal->getDef('paypal_primary_email_address_desc'); ?></span>
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
      <?php echo $CLICSHOPPING_PayPal->getDef('paypal_sandbox_title'); ?>
    </div>
    <div class="card-block">

      <div class="row">
        <div class="col-md-12">
          <div class="form-group row">
            <label for="sandbox_username"
                   class="col-5 col-form-label"><?php echo $CLICSHOPPING_PayPal->getDef('paypal_api_username'); ?></label>
            <div class="col-md-12">
              <?php echo HTML::inputField('sandbox_username', CLICSHOPPING_APP_PAYPAL_SANDBOX_API_USERNAME, 'id="sandbox_username"'); ?>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group row">
            <label for="sandbox_password"
                   class="col-5 col-form-label"><?php echo $CLICSHOPPING_PayPal->getDef('paypal_api_password'); ?></label>
            <div class="col-md-12">
              <?php echo HTML::inputField('sandbox_password', CLICSHOPPING_APP_PAYPAL_SANDBOX_API_PASSWORD, 'id="sandbox_password"'); ?>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group row">
            <label for="sandbox_signature"
                   class="col-5 col-form-label"><?php echo $CLICSHOPPING_PayPal->getDef('paypal_api_signature'); ?></label>
            <div class="col-md-12">
              <?php echo HTML::inputField('sandbox_signature', CLICSHOPPING_APP_PAYPAL_SANDBOX_API_SIGNATURE, 'id="sandbox_signature"'); ?>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group row">
            <label for="sandbox_merchant_id"
                   class="col-5 col-form-label"><?php echo $CLICSHOPPING_PayPal->getDef('paypal_merchant_id'); ?></label>
            <div class="col-md-12">
              <?php echo HTML::inputField('sandbox_merchant_id', CLICSHOPPING_APP_PAYPAL_SANDBOX_MERCHANT_ID, 'id="sandbox_merchant_id"'); ?>
              <span class="form-text"><?php echo $CLICSHOPPING_PayPal->getDef('paypal_merchant_id_desc'); ?></span>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group row">
            <label for="sandbox_email"
                   class="col-5 col-form-label"><?php echo $CLICSHOPPING_PayPal->getDef('paypal_email_address'); ?></label>
            <div class="col-md-12">
              <?php echo HTML::inputField('sandbox_email', CLICSHOPPING_APP_PAYPAL_SANDBOX_SELLER_EMAIL, 'id="sandbox_email"'); ?>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group row">
            <label for="sandbox_email_primary"
                   class="col-5 col-form-label"><?php echo $CLICSHOPPING_PayPal->getDef('paypal_primary_email_address'); ?></label>
            <div class="col-md-12">
              <?php echo HTML::inputField('sandbox_email_primary', CLICSHOPPING_APP_PAYPAL_SANDBOX_SELLER_EMAIL_PRIMARY, 'id="sandbox_email_primary"'); ?>
              <span
                class="form-text"><?php echo $CLICSHOPPING_PayPal->getDef('paypal_primary_email_address_desc'); ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>