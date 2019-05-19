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

  require_once(__DIR__ . '/template_top.php');
?>


  <div class="col-sm-6 float-md-right">
    <div class="card">
      <div class="card-header">
        <?php echo $CLICSHOPPING_PayPal->getDef('online_forum_title'); ?>
      </div>
      <div class="card-block">
        <p class="card-text">
          <?php echo
          $CLICSHOPPING_PayPal->getDef('online_forum_body', [
            'button_online_forum' => HTML::button($CLICSHOPPING_PayPal->getDef('button_online_forum'), null, 'https://www.clicshopping.org/', 'info', ['newwindow' => 'blank'])
          ]);
          ?>
        </p>
      </div>
    </div>
    <div class="separator"></div>
  </div>

  <div class="col-sm-6 float-md-right">
    <div class="card">
      <div class="card-header">
        <?php echo $CLICSHOPPING_PayPal->getDef('title_intro_document'); ?>
      </div>
      <div class="card-block">
        <p class="card-text">
          <?php echo $CLICSHOPPING_PayPal->getDef('text_intro_document'); ?>
        </p>
      </div>
    </div>
    <div class="separator"></div>
  </div>

  <div class="clearfix"></div>
  <div class="separator"></div>
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <?php echo $CLICSHOPPING_PayPal->getDef('title_api_credentials'); ?>
      </div>
      <div class="card-block">
        <p class="card-text">
          <?php echo $CLICSHOPPING_PayPal->getDef('text_api_credentials'); ?>
        </p>
      </div>
    </div>
    <div class="separator"></div>
  </div>

  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <?php echo $CLICSHOPPING_PayPal->getDef('title_express_checkout'); ?>
      </div>
      <div class="card-block">
        <p class="card-text">
          <?php echo $CLICSHOPPING_PayPal->getDef('text_express_checkout'); ?>
        </p>
      </div>
    </div>
    <div class="separator"></div>
  </div>


  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <?php echo $CLICSHOPPING_PayPal->getDef('title_direct_payment'); ?>
      </div>
      <div class="card-block">
        <p class="card-text">
          <?php echo $CLICSHOPPING_PayPal->getDef('text_direct_payment'); ?>
        </p>
      </div>
    </div>
    <div class="separator"></div>
  </div>


  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <?php echo $CLICSHOPPING_PayPal->getDef('title_hosted_solution'); ?>
      </div>
      <div class="card-block">
        <p class="card-text">
          <?php echo $CLICSHOPPING_PayPal->getDef('text_hosted_solution'); ?>
        </p>
      </div>
    </div>
    <div class="separator"></div>
  </div>


  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <?php echo $CLICSHOPPING_PayPal->getDef('title_paypal_standard'); ?>
      </div>
      <div class="card-block">
        <p class="card-text">
          <?php echo $CLICSHOPPING_PayPal->getDef('text_paypal_standard'); ?>
        </p>
      </div>
    </div>
    <div class="separator"></div>
  </div>

  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <?php echo $CLICSHOPPING_PayPal->getDef('title_log_in_payal'); ?>
      </div>
      <div class="card-block">
        <p class="card-text">
          <?php echo $CLICSHOPPING_PayPal->getDef('text_log_in_paypal'); ?>
        </p>
      </div>
    </div>
    <div class="separator"></div>
  </div>

  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <?php echo $CLICSHOPPING_PayPal->getDef('title_order_paypal'); ?>
      </div>
      <div class="card-block">
        <p class="card-text">
          <?php echo $CLICSHOPPING_PayPal->getDef('text_order_paypal'); ?>
        </p>
      </div>
    </div>
    <div class="separator"></div>
  </div>

  <div class="text-md-center">
    <small><?php echo $CLICSHOPPING_PayPal->getDef('text_license'); ?></small>
  </div>

<?php
  require_once(__DIR__ . '/template_bottom.php');