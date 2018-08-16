<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */

use ClicShopping\OM\CLICSHOPPING;
?>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
  <div class="card">
    <div class="card-header"><?php echo CLICSHOPPING::getDef('module_checkout_success_text_success'); ?></div>
    <div class="separator"></div>
    <div class="col-md-12">
      <div><?php echo CLICSHOPPING::getDef('module_checkout_success_text_thanks_for_shopping', ['store_name' => STORE_NAME]); ?></div>
      <div class="separator"></div>
      <div class="hr"></div>
      <div class="ClicShoppingCheckoutSuccessText">
      <span>

<?php echo sprintf(CLICSHOPPING::getDef('module_checkout_success_text_see_orders', ['store_name' => STORE_NAME, 'store_name_address' => STORE_NAME_ADDRESS,
                                                                             'account_history' => '<a href="' . CLICSHOPPING::link('index.php', 'Account&History') . '">' . CLICSHOPPING::getDef('module_checkout_success_text_order_history') . '</a>',
                                                                             'my_account' => '<a href="' . CLICSHOPPING::link('index.php', 'Account&Main') . '">' . CLICSHOPPING::getDef('module_checkout_success_text_account') . '</a>',
                                                                            ]
                                ), CLICSHOPPING::link('index.php', 'Account&HistoryInfo')
                  );
?>
      </span>
      <div class="hr"></div>
<?php
  echo sprintf(CLICSHOPPING::getDef('module_checkout_success_text_contact_store_owner', ['store_name' => STORE_NAME,
                                                                                  'account_history' => '<a href="' . CLICSHOPPING::link('index.php', 'Account&History') . '">' . CLICSHOPPING::getDef('module_checkout_success_text_order_history') . '</a>',
                                                                                  'contact' => '<a href="index.php?Info&Contact">' . CLICSHOPPING::getDef('module_checkout_success_text_contact') . '</a>'
                                                                                 ]
                            ), CLICSHOPPING::link('index.php','info&Contact')
              );
?>
      </div>
    </div>
  </div>
      <div class="separator"></div>

</div>
