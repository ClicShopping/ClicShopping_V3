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
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_PayPal = Registry::get('PayPal');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
?>

<script>
    var CLICSHOPPING = {
        htmlSpecialChars: function (string) {
            if (string == null) {
                string = '';
            }

            return $('<span />').text(string).html();
        },
        nl2br: function (string) {
            return string.replace(/\n/g, '<br />');
        },
        APP: {
            PAYPAL: {
                action: '<?php echo isset($CLICSHOPPING_Page->data['action']) ? $CLICSHOPPING_Page->data['action'] : ''; ?>',
                accountTypes: {
                    live: <?php echo ($CLICSHOPPING_PayPal->hasApiCredentials('live') === true) ? 'true' : 'false'; ?>,
                    sandbox: <?php echo ($CLICSHOPPING_PayPal->hasApiCredentials('sandbox') === true) ? 'true' : 'false'; ?>
                }
            }
        }
    };
</script>

<?php
  if ($CLICSHOPPING_MessageStack->exists('PayPal')) {
    echo $CLICSHOPPING_MessageStack->get('PayPal');
  }
?>

<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/modules_modules_checkout_payment.gif', $CLICSHOPPING_PayPal->getDef('Paypal'), '40', '40'); ?></span>
          <span
            class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_PayPal->getDef('Paypal') . ' v' . $CLICSHOPPING_PayPal->getVersion(); ?></span>
          <span class="col-md-7 text-md-right">
          <span class="text-md-right"
                style="padding-left:5px;"><?php echo '<a href="' . $CLICSHOPPING_PayPal->link('Info') . '">' . $CLICSHOPPING_PayPal->getDef('app_link_info') . '</a> <a href="' . $CLICSHOPPING_PayPal->link('Privacy') . '">' . $CLICSHOPPING_PayPal->getDef('app_link_privacy') . '</a>'; ?></span>
        </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>

  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">

          <span class="col-md-2">
            <?php echo HTML::button($CLICSHOPPING_PayPal->getDef('button_balance'), null, $CLICSHOPPING_PayPal->link('Balance'), 'primary'); ?>
          </span>
          <span class="col-md-2">
            <?php echo HTML::button($CLICSHOPPING_PayPal->getDef('button_configure'), null, $CLICSHOPPING_PayPal->link('Configure'), 'warning'); ?>
          </span>
          <span class="col-md-2">
             <?php echo HTML::button($CLICSHOPPING_PayPal->getDef('button_credential'), null, $CLICSHOPPING_PayPal->link('Credentials'), 'info'); ?>
          </span>
          <span class="col-md-2">
             <?php echo HTML::button($CLICSHOPPING_PayPal->getDef('button_log'), null, $CLICSHOPPING_PayPal->link('Log'), 'danger'); ?>
          </span>
          <span class="col-md-2">
             <?php echo HTML::button($CLICSHOPPING_PayPal->getDef('button_delete_menu'), null, $CLICSHOPPING_PayPal->link('Configure&DeleteMenu'), 'danger'); ?>
          </span>
          <span class="col-md-2">
            <?php echo HTML::button($CLICSHOPPING_PayPal->getDef('button_sort_order'), null, CLICSHOPPING::link(null, 'A&Configuration\Modules&Modules&set=payment'), 'primary'); ?>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
