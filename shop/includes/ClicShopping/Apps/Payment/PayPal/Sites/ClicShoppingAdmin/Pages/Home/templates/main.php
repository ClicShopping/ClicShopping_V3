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

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  $CLICSHOPPING_PayPal = Registry::get('PayPal');

  require_once(__DIR__ . '/template_top.php');
?>
  <div class="contentBody">
    <div class="separator"></div>

    <div class="row">
      <div class="col-md-12">
        <div class="card card-block headerCard">
          <div class="row">
            <div class="col-md-12"><?php echo '&nbsp;' . $CLICSHOPPING_PayPal->getDef('onboarding_intro_body'); ?></div>

            <div class="col-md-12 text-md-right">
              <?php echo
              $CLICSHOPPING_PayPal->getDef('manage_credentials_body', [
                'button_manage_credentials' => HTML::button($CLICSHOPPING_PayPal->getDef('button_manage_credentials'), null, $CLICSHOPPING_PayPal->link('Credentials'), 'info')
              ]);
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php
  require_once(__DIR__ . '/template_bottom.php');